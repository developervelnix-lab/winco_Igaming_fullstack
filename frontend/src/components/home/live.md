"use client"

import { useState, useEffect } from "react"
import { FaArrowLeft, FaPlay } from "react-icons/fa"
import { liveSport } from "../jsondata/live"
import { turbogames } from "../jsondata/turbogames"
import { useNavigate } from "react-router-dom"
import { API_URL } from "@/utils/constants"
import { COLORS, FONTS } from "../../constants/theme"

const GameSection = ({ title, games }) => {
  const [preloadedImages, setPreloadedImages] = useState([])
  const [loadingForGames, setLoadingForGames] = useState(null)
  const [showPopup, setShowPopup] = useState(false)
  const navigate = useNavigate()
  const [confirmPopup, setConfirmPopup] = useState({ show: false, game: null })
  const [hoveredGame, setHoveredGame] = useState(null)
  const [confirmLoading, setConfirmLoading] = useState(false)
  const [loadingProgress, setLoadingProgress] = useState(0)

  useEffect(() => {
    const images = games.map((game) => game.icon)
    preloadImages(images)
  }, [games])

  // Effect to simulate loading progress
  useEffect(() => {
    if (confirmLoading) {
      setLoadingProgress(0)
      const interval = setInterval(() => {
        setLoadingProgress((prev) => {
          // Increase progress but cap at 90% to wait for actual completion
          const newProgress = prev + Math.random() * 15
          return newProgress > 90 ? 90 : newProgress
        })
      }, 300)

      return () => clearInterval(interval)
    } else if (loadingProgress > 0) {
      // When loading completes, quickly fill to 100%
      setLoadingProgress(100)
      const timeout = setTimeout(() => {
        setLoadingProgress(0)
      }, 500)

      return () => clearTimeout(timeout)
    }
  }, [confirmLoading])

  const preloadImages = async (imageUrls) => {
    try {
      const promises = imageUrls.map(
        (imageUrl) =>
          new Promise((resolve, reject) => {
            const img = new Image()
            img.src = imageUrl
            img.onload = resolve
            img.onerror = reject
          }),
      )
      await Promise.all(promises)
      setPreloadedImages(imageUrls)
    } catch (error) {
      console.error("Error preloading images:", error)
    }
  }

  const handleGameClick = (game) => {
    setConfirmPopup({ show: true, game })
  }

  const confirmGameOpen = async () => {
    const authSecretKey = sessionStorage.getItem("auth_secret_key")
    const userId = sessionStorage.getItem("account_id")

    const game = confirmPopup.game
    setLoadingForGames(game["Game UID"])
    setConfirmLoading(true) // Set loading state to true when starting
    setConfirmPopup({ show: false, game: confirmPopup.game }) // Hide confirmation popup but keep game info

    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          route: "route-play-games",
          AuthToken: authSecretKey,
        },
        body: JSON.stringify({
          USER_ID: userId,
          GAME_NAME: game["Game Name"],
          GAME_UID: game["Game UID"],
        }),
      })

      if (!response.ok) {
        throw new Error("Network response was not ok")
      }

      const data = await response.json()

      if (showPopup) {
        setShowPopup(false)
      }

      if (data.error) {
        console.error("Error:", data.status_code || data.error)
      } else if (data.data?.game_url) {
        // Small delay to ensure loading animation is seen
        setTimeout(() => {
          navigate(`/game-url/${encodeURIComponent(data.data.game_url)}/${encodeURIComponent(game["Game Name"])}`)
        }, 500)
      } else {
        console.error("No game URL in the response.")
      }
    } catch (error) {
      console.error("Error logging game click:", error)
    } finally {
      setTimeout(() => {
        setLoadingForGames(null)
        setConfirmLoading(false) // Reset loading state when done
        setConfirmPopup({ show: false, game: null })
      }, 500)
    }
  }

  return (
    <div
      className="game-section relative w-full px-4 py-5 rounded-2xl shadow-2xl overflow-hidden transition-all duration-300 border border-white/5"
      style={{
        backgroundColor: `${COLORS.bg2}EE`,
        backdropFilter: "blur(10px)",
      }}
    >
      {/* Decorative Brand Accent */}
      <div
        className="absolute top-0 left-0 w-1.5 h-full opacity-70"
        style={{ background: COLORS.brandGradient }}
      ></div>

      <div className="flex justify-between items-center mb-6 pl-2">
        <h2
          className="text-white text-lg font-bold tracking-tight uppercase"
          style={{ fontFamily: FONTS.head, letterSpacing: '0.05em' }}
        >
          {title}
        </h2>
        <div className="flex items-center space-x-3">

        </div>
      </div>

      <div className="grid grid-cols-5 gap-4 px-2">
        {liveSport.slice(0, 5).map((game, index) => (
          <div
            key={index}
            className="relative group w-full aspect-[4/5] rounded-xl overflow-hidden border border-white/5 hover:border-white/20 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl"
          >
            <img
              className={`absolute inset-0 w-full h-full object-cover transition-all duration-700 group-hover:scale-110 ${loadingForGames === game["Game UID"] ? "opacity-50" : ""
                }`}
              src={game.icon || "/placeholder.svg"}
              alt={game["Game Name"]}
              onClick={() => handleGameClick(game)}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3 pointer-events-none">
              <span className="text-white text-[10px] font-bold uppercase tracking-wider truncate w-full" style={{ fontFamily: FONTS.ui }}>
                {game["Game Name"]}
              </span>
            </div>
            <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
              <div
                className="p-3 rounded-full transform scale-0 group-hover:scale-100 transition-transform duration-500 shadow-2xl"
                style={{ background: COLORS.brandGradient }}
              >
                <FaPlay className="text-white ml-0.5" size={14} />
              </div>
            </div>
          </div>
        ))}
      </div>

      {showPopup && (
        <div
          className="fixed inset-0 z-50 overflow-y-auto backdrop-blur-3xl animate-fadeIn"
          style={{ backgroundColor: `${COLORS.bg}F2` }}
        >
          <div className="max-w-screen-2xl mx-auto px-6 py-10">
            <div className="flex justify-between items-center mb-10">
              <div className="flex items-center gap-6">
                <button
                  onClick={() => setShowPopup(false)}
                  className="bg-white/5 hover:bg-white/10 text-white rounded-full p-3 transition-all duration-300 border border-white/10 shadow-lg active:scale-90"
                  aria-label="Close"
                >
                  <FaArrowLeft size={18} />
                </button>
                <div
                  className="h-10 w-1 opacity-70"
                  style={{ background: COLORS.brandGradient }}
                ></div>
                <h2
                  className="text-2xl font-black text-white tracking-widest uppercase"
                  style={{ fontFamily: FONTS.head }}
                >
                  {title}
                </h2>
              </div>
            </div>

            <div className="flex flex-wrap gap-3">
              {games.map((game, index) => (
                <div key={index} className="w-[30%] md:w-[15.5%] lg:w-[11.5%] flex flex-col items-center">
                  <div className="w-full aspect-square relative">
                    <div
                      className="relative group w-full"
                      onMouseEnter={() => setHoveredGame(game["Game UID"])}
                      onMouseLeave={() => setHoveredGame(null)}
                    >
                      <img
                        className={`${loadingForGames === game["Game UID"] ? "opacity-50" : ""
                          } rounded-lg cursor-pointer w-full transition-all duration-300 group-hover:brightness-75`}
                        src={game.icon || "/placeholder.svg"}
                        alt={game["Game Name"]}
                        onClick={() => handleGameClick(game)}
                      />
                      {hoveredGame === game["Game UID"] && (
                        <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                          <div className="bg-black bg-opacity-60 rounded-full p-3">
                            <FaPlay className="text-white text-opacity-90" size={16} />
                          </div>
                        </div>
                      )}
                    </div>
                    <div className="w-full mt-2 overflow-hidden">
                      <p className="text-sm font-medium text-center text-white truncate">{game["Game Name"]}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {confirmPopup.show && (
        <div className="fixed inset-0 flex items-center justify-center bg-black/80 backdrop-blur-md z-[99999] transition-opacity duration-500">
          <div
            className="relative p-8 rounded-3xl shadow-3xl max-w-sm w-full mx-6 animate-fadeInUp border border-white/10 text-center"
            style={{ backgroundColor: COLORS.bg2 }}
          >
            {/* Decorative Icon */}
            <div
              className="absolute -top-10 left-1/2 transform -translate-x-1/2 p-5 rounded-full shadow-2xl animate-bounce-slow"
              style={{ background: COLORS.brandGradient }}
            >
              <FaPlay className="text-white ml-0.5" size={24} />
            </div>

            {/* Confirmation Message */}
            <p
              className="text-white text-lg font-bold mt-8 mb-2 tracking-tight"
              style={{ fontFamily: FONTS.head }}
            >
              Ready to Win?
            </p>
            <p className="text-white/60 text-sm mb-8 leading-relaxed">
              You are about to enter <span className="text-white font-bold">{confirmPopup.game?.["Game Name"]}</span>. Good luck!
            </p>

            {/* Action Buttons */}
            <div className="flex flex-col gap-3">
              <button
                onClick={confirmGameOpen}
                className="w-full px-6 py-4 rounded-2xl font-bold uppercase tracking-widest transition-all duration-300 shadow-lg active:scale-95 group overflow-hidden relative text-white"
                style={{ background: COLORS.brandGradient, fontFamily: FONTS.ui }}
              >
                <div className="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <span>Confirm Play</span>
              </button>

              <button
                onClick={() => setConfirmPopup({ show: false, game: null })}
                className="w-full px-6 py-3 rounded-2xl font-bold uppercase tracking-widest bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition-all duration-300 border border-white/5"
                style={{ fontFamily: FONTS.ui }}
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Game Loading Screen */}
      {confirmLoading && (
        <div className="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-md z-[999999] flex flex-col items-center justify-center">
          <div className="w-full max-w-md px-6 py-8 text-center">
            {/* Game icon and name */}
            <div className="mb-6 flex flex-col items-center">
              {confirmPopup.game && (
                <>
                  <div className="w-24 h-24 mb-4 rounded-lg overflow-hidden border-4 border-blue-500 shadow-lg">
                    <img
                      src={confirmPopup.game.icon || "/placeholder.svg"}
                      alt={confirmPopup.game["Game Name"]}
                      className="w-full h-full object-cover"
                    />
                  </div>
                  <h3 className="text-xl font-bold text-white mb-1">{confirmPopup.game["Game Name"]}</h3>
                </>
              )}
              <p className="text-blue-400 text-sm animate-pulse">Opening game...</p>
            </div>

            {/* Progress bar */}
            <div className="w-full bg-gray-700 rounded-full h-3 mb-6 overflow-hidden">
              <div
                className="bg-gradient-to-r from-blue-500 to-blue-300 h-full rounded-full transition-all duration-300 ease-out"
                style={{ width: `${loadingProgress}%` }}
              ></div>
            </div>

            {/* Loading messages */}
            <div className="text-white text-sm mb-8">
              <div className="flex justify-between mb-2">
                <span>Connecting to server...</span>
                <span className={loadingProgress > 30 ? "text-green-400" : "text-gray-400"}>
                  {loadingProgress > 30 ? "✓" : "..."}
                </span>
              </div>
              <div className="flex justify-between mb-2">
                <span>Loading game assets...</span>
                <span className={loadingProgress > 60 ? "text-green-400" : "text-gray-400"}>
                  {loadingProgress > 60 ? "✓" : "..."}
                </span>
              </div>
              <div className="flex justify-between">
                <span>Preparing game environment...</span>
                <span className={loadingProgress > 85 ? "text-green-400" : "text-gray-400"}>
                  {loadingProgress > 85 ? "✓" : "..."}
                </span>
              </div>
            </div>

            {/* Tips */}
            <div className="bg-gray-800 bg-opacity-50 p-4 rounded-lg border border-gray-700">
              <h4 className="text-gray-300 text-xs uppercase tracking-wider mb-2">TIP</h4>
              <p className="text-gray-400 text-sm">
                Make sure to check the game rules before playing to maximize your winning chances!
              </p>
            </div>
          </div>

          {/* Animated elements */}
          <div className="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex space-x-3">
            <div className="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style={{ animationDelay: "0ms" }}></div>
            <div className="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style={{ animationDelay: "150ms" }}></div>
            <div className="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style={{ animationDelay: "300ms" }}></div>
          </div>
        </div>
      )}
    </div>
  )
}

const Live = () => {
  return (
    <div className="games-display space-y-6">
      <GameSection title="🔴 Popular Games " games={turbogames} />
    </div>
  )
}

export default Live