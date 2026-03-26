/*
  Author: DevKilla
  Buy Code From: jinkteam.com
  Contact: @devkilla (Telegram)
*/


"use client"

import { useState, useEffect } from "react"
import { FaArrowLeft, FaWallet, FaExpand, FaCompress } from "react-icons/fa"
import { useParams, useNavigate } from "react-router-dom"

const GameplayComponent = () => {
  const { gameUrl, gameName } = useParams()
  const navigate = useNavigate()
  const [isFullscreen, setIsFullscreen] = useState(false)
  const [isHeaderVisible, setIsHeaderVisible] = useState(true)
  const [mouseIdle, setMouseIdle] = useState(false)

  // Handle back navigation
  const handleBack = () => {
    navigate("/")
  }

  // Handle deposit button click
  const handleDeposit = () => {
    navigate("/deposit")
  }

  // Toggle fullscreen
  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      document.documentElement
        .requestFullscreen()
        .then(() => {
          setIsFullscreen(true)
        })
        .catch((err) => {
          console.error(`Error attempting to enable fullscreen: ${err.message}`)
        })
    } else {
      if (document.exitFullscreen) {
        document
          .exitFullscreen()
          .then(() => {
            setIsFullscreen(false)
          })
          .catch((err) => {
            console.error(`Error attempting to exit fullscreen: ${err.message}`)
          })
      }
    }
  }

  // Auto-hide header when mouse is idle
  useEffect(() => {
    let timeout

    const resetTimer = () => {
      clearTimeout(timeout)
      setMouseIdle(false)
      setIsHeaderVisible(true)

      timeout = setTimeout(() => {
        setMouseIdle(true)
        setIsHeaderVisible(false)
      }, 3000) // Hide after 3 seconds of inactivity
    }

    window.addEventListener("mousemove", resetTimer)
    resetTimer()

    return () => {
      window.removeEventListener("mousemove", resetTimer)
      clearTimeout(timeout)
    }
  }, [])

  return (
    <div className="fixed inset-0 flex flex-col bg-gray-900">
      {/* Header - animated to slide up/down */}
      <div
        className={`bg-gray-800 text-black dark:text-white p-3 flex justify-between items-center z-20 transition-transform duration-300 ease-in-out ${
          isHeaderVisible ? "transform translate-y-0" : "transform -translate-y-full"
        }`}
      >
        <div className="flex items-center gap-3">
          <button
            onClick={handleBack}
            className="flex items-center justify-center bg-gray-700 hover:bg-gray-600 w-10 h-10 rounded-full transition-all duration-300"
            title="Back to games"
          >
            <FaArrowLeft size={16} />
          </button>
          <h1 className="text-xl font-medium truncate max-w-[200px] sm:max-w-none">{decodeURIComponent(gameName)}</h1>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={toggleFullscreen}
            className="flex items-center justify-center bg-gray-700 hover:bg-gray-600 w-10 h-10 rounded-full transition-all duration-300"
            title={isFullscreen ? "Exit fullscreen" : "Enter fullscreen"}
          >
            {isFullscreen ? <FaCompress size={16} /> : <FaExpand size={16} />}
          </button>

          <button
            onClick={handleDeposit}
            className="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-black dark:text-white rounded-full font-medium py-2 px-5 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            <FaWallet size={16} />
            <span>Deposit</span>
          </button>
        </div>
      </div>

      {/* Game container */}
      <div className="flex-1 w-full relative">
        {/* Show a hint to move mouse when header is hidden */}
        {mouseIdle && (
          <div className="absolute top-2 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-70 text-black dark:text-white text-sm py-1 px-3 rounded-full z-10 animate-pulse">
            Move mouse to show controls
          </div>
        )}

        <iframe
          src={decodeURIComponent(gameUrl)}
          title={decodeURIComponent(gameName)}
          className="w-full h-full border-0"
          allow="fullscreen; accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowFullScreen
        ></iframe>
      </div>
    </div>
  )
}

export default GameplayComponent

