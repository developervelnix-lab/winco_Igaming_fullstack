"use client"

import { useState, useEffect } from "react"
import FrontScrollableCard from "./FrontScrollableCard"
import Navbar from "../navbar/Navbar"
import TrendingSlot from "./TrendingSlot"
import GamesDisplay from "./GameDisplay"
import GameProvider from "./GameProvider"
import Faq from "./Faq"
import MobileFooterNav from "../navbar/MobileFooterNav"
import Live from "./Live"
import Turbogames from "./Turbogames"
import FeaturesSection from "./FeaturesSection"
import PromotionSection from "./PromotionSection"
import NewsTicker from "./NewsTicker"
import Footer from "./Footer"
import { useColors } from '../../hooks/useColors'
import { useSite } from "../../context/SiteContext"
import { URL as BASE_URL } from "../../utils/constants"

function Home() {
  const COLORS = useColors()
  const { accountInfo, promoBanners, heroBanners, loading } = useSite()
  const [showToast, setShowToast] = useState(false)
  const [showSuccessToast, setShowSuccessToast] = useState(false)
  
  const [isAuthenticated, setIsAuthenticated] = useState(false)

  const checkAuth = () => {
    const authSecretKey = sessionStorage.getItem("auth_secret_key")
    const wasAuthenticated = isAuthenticated
    const isNowAuthenticated = !!authSecretKey

    setIsAuthenticated(isNowAuthenticated)

    if (isNowAuthenticated && !wasAuthenticated) {
      setShowSuccessToast(true)
      setShowToast(false)
      setTimeout(() => setShowSuccessToast(false), 5000)
    } else if (!isNowAuthenticated && wasAuthenticated) {
      setShowToast(true)
      setTimeout(() => setShowToast(false), 3000)
    } else {
      setShowToast(false)
    }
  }

  useEffect(() => {
    const timer = setTimeout(() => {
      setLoading(false)
      
      const justLoggedIn = sessionStorage.getItem("just_logged_in") === "true"
      const authSecretKey = sessionStorage.getItem("auth_secret_key")
      const isLoggedIn = !!authSecretKey

      setIsAuthenticated(isLoggedIn)

      if (isLoggedIn) {
        if (justLoggedIn) {
          setShowSuccessToast(true)
          sessionStorage.removeItem("just_logged_in")
          setTimeout(() => setShowSuccessToast(false), 5000)
        }
      }
    }, 10)

    return () => clearTimeout(timer)
  }, [])

  useEffect(() => {
    const handleStorageChange = (e) => {
      if (e.key === "auth_secret_key") {
        checkAuth()
      }
    }

    window.addEventListener("storage", handleStorageChange)
    return () => window.removeEventListener("storage", handleStorageChange)
  }, [isAuthenticated])

  useEffect(() => {
    if (window.location.hash) {
      const id = window.location.hash.substring(1)
      const element = document.getElementById(id)
      if (element) {
        setTimeout(() => {
          element.scrollIntoView({ behavior: "smooth" })
        }, 500)
      }
    }
  }, [])

  return (
    <div className="flex flex-col min-h-screen relative">
      {showToast && !isAuthenticated && <ToastMessage onClose={() => setShowToast(false)} />}
      {showSuccessToast && <SuccessToastMessage onClose={() => setShowSuccessToast(false)} />}

      <Navbar externalAccountInfo={accountInfo} />
      <main className="flex-grow" style={{ backgroundColor: COLORS.bg }}>
        <div className="w-full relative">
          <FrontScrollableCard banners={heroBanners} />
        </div>
        <NewsTicker />
        <div className="px-4 md:px-8 mt-4 md:mt-6">
          <div className="mb-6" id="slots-trending">
            <TrendingSlot />
          </div>
          <div className="mb-6" id="live">
            <Live />
          </div>
          <div className="mb-6" id="aviator">
            <Turbogames />
          </div>
          <div className="mb-6">
            <GamesDisplay />
          </div>
          <div className="mb-6" id="promotions">
            <PromotionSection banners={promoBanners} />
          </div>
          <div className="mb-10">
            <GameProvider />
          </div>
        </div>
      </main>
      <div className="px-4 md:px-8 mb-8">
        <FeaturesSection />
      </div>
      <div className="px-4 md:px-8 mb-6">
        <Faq />
      </div>
      <Footer accountInfo={accountInfo} />
      <footer className="md:hidden fixed bottom-0 left-0 right-0 z-50">
        <MobileFooterNav />
      </footer>
    </div>
  )
}

function LogoLoader() {
  const COLORS = useColors()
  const { accountInfo } = useSite()
  const logoUrl = accountInfo?.service_site_logo 
    ? `${BASE_URL}${encodeURI(accountInfo.service_site_logo)}`
    : "";

  return (
    <div className="fixed inset-0 flex items-center justify-center z-50" style={{ backgroundColor: COLORS.bg }}>
      <div className="flex flex-col items-center">
        <div className="relative mb-8">
          <div className="relative w-24 h-24 flex items-center justify-center">
            {logoUrl && <img src={logoUrl} alt="Loading" className="w-12 h-12 object-contain" />}
            <div className="absolute inset-0 border-4 rounded-full animate-spin" style={{ borderColor: `${COLORS.brand}33`, borderTopColor: COLORS.brand }}></div>
          </div>
        </div>
        <div className="text-center space-y-2">
          <div className="text-4xl md:text-5xl font-black text-black dark:text-white uppercase tracking-[0.2em] hero-text-main">
            {accountInfo?.service_site_name || "Site"}
          </div>
          <div className="text-xs md:text-sm font-bold uppercase tracking-[0.4em]" style={{ color: `${COLORS.brand}99` }}>
            PREMIER BETTING EXPERIENCE
          </div>
        </div>
      </div>
    </div>
  )
}

function ToastMessage({ onClose }) {
  return (
    <div className="toast-message fixed top-8 left-1/2 transform -translate-x-1/2 p-4 bg-red-600 text-black dark:text-white flex justify-between items-center animate-slide-in rounded-lg shadow-lg w-[90%] md:w-[400px] z-[1000]">
      <span className="flex-1 text-center">Please login to play. Minimum deposit ₹100 required.</span>
      <button onClick={onClose} className="ml-4 text-black dark:text-white font-bold">
        &times;
      </button>
    </div>
  )
}

function SuccessToastMessage({ onClose }) {
  const COLORS = useColors()
  return (
    <div
      className="toast-message fixed top-8 left-1/2 transform -translate-x-1/2 p-4 text-black dark:text-white flex justify-between items-center animate-slide-in rounded-lg shadow-lg w-[90%] md:w-[400px] z-[1000]"
      style={{ backgroundColor: `${COLORS.brand}CC` }}
    >
      <span className="flex-1 text-center">Successfully logged in. Welcome!</span>
      <button onClick={onClose} className="ml-4 text-black dark:text-white font-bold">
        &times;
      </button>
    </div>
  )
}


export default Home
