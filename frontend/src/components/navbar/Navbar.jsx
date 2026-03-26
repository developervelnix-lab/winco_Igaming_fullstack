"use client"

import { useEffect, useState } from "react"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faBars, faTimes } from "@fortawesome/free-solid-svg-icons"
import Login from "../auth/Login"
import Register from "../auth/Register"
import { useNavigate } from "react-router-dom"
import AccountInfo from "./AccountInfo"
import { allsport } from "../jsondata/sport"
import { FaSignInAlt, FaUserPlus, FaMoneyCheckAlt, FaWallet, FaHeadset, FaDownload, FaExchangeAlt, FaHistory, FaGavel, FaShieldAlt, FaLock, FaUserShield, FaGift, FaStar, FaShareAlt, FaKey, FaSignOutAlt, FaWhatsapp, FaTelegramPlane, FaInstagram, FaFacebookF, FaTwitter, FaBars, FaTimes, FaUserCircle, FaSun, FaMoon, FaEnvelope } from "react-icons/fa"
import { useSite } from "../../context/SiteContext"
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';
import { useTheme } from "../../context/ThemeContext"
import { URL as BASE_URL } from "../../utils/constants"

function Navbar() {
  const { accountInfo, showLogin, setShowLogin, showRegister, setShowRegister } = useSite();
  const authSecretKey = sessionStorage.getItem("auth_secret_key")
  const userId = sessionStorage.getItem("account_id")
  const [menuOpen, setMenuOpen] = useState(false)
  const [profileOpen, setProfileOpen] = useState(false)
  const [selectedGame, setSelectedGame] = useState(null)
  const navigate = useNavigate()
  const [toast, setToast] = useState(null)
  const { theme, toggleTheme } = useTheme()
  const COLORS = useColors();

  // Add effect to prevent body scrolling when menu is open
  useEffect(() => {
    if (menuOpen) {
      document.body.style.overflow = "hidden"
    } else {
      document.body.style.overflow = ""
    }

    return () => {
      document.body.style.overflow = ""
    }
  }, [menuOpen])

  const showToast = (type, message) => {
    setToast({ type, message })
    setTimeout(() => setToast(null), 3000) // Automatically dismiss toast after 3 seconds
  }

  // Game Categories with icons matching the neon design
  const games = [
    { name: "Cricket", icon: "🏏" },
    { name: "Football", icon: "⚽" },
    { name: "Tennis", icon: "🎾" },
    { name: "Basketball", icon: "🏀" },
    { name: "Hockey", icon: "🏑" },
    { name: "Rugby", icon: "🏉" },
    { name: "Esports", icon: "🎮" },
    { name: "MMA", icon: "🥋" },
    { name: "Boxing", icon: "🥊" },
    { name: "Volleyball", icon: "🏐" },
  ]
  const handleGameSelect = async (index) => {
    setSelectedGame(index);

    // Check if user is logged in first
    if (!authSecretKey) {
      setShowLogin(true)
      return
    }

    console.log(allsport['Game UID'])
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
          GAME_NAME: allsport["Game Name"],
          GAME_UID: allsport["Game UID"],
        }),
      });

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const data = await response.json();
      if (data.error) {
        console.error("Error:", data.status_code || data.error);
      } else if (data.data?.game_url) {
        navigate(`/game-url/${encodeURIComponent(data.data.game_url)}/${encodeURIComponent(allsport["Game Name"])}`);
      } else {
        console.error("No game URL in the response.");
      }
    } catch (error) {
      console.error("Error logging game click:", error);
    }
  };
  const handleLoginClick = () => {
    setShowLogin(true)
    setMenuOpen(false) // Close the sidebar if open
  }

  const handleRegisterClick = () => {
    setShowRegister(true)
    setMenuOpen(false) // Close the sidebar if open
  }

  const closeLoginModal = () => {
    setShowRegister(false)
    setShowLogin(false)
  }

  const closeRegisterModal = () => {
    setShowLogin(false)
    setShowRegister(false)
  }

  function handleTransactionClick() {
    setMenuOpen(false)
    navigate("/transaction")
  }
  function handleBettingTransaction() {
    setMenuOpen(false)
    navigate("/betting-profit-loss")
  }

  function handleChangePassword() {
    setMenuOpen(false)
    navigate("/change-password")
  }

  function handleOpenBetClick() {
    setMenuOpen(false)
    navigate("/openbet")
  }
  function handleRulesRegulation() {
    setMenuOpen(false)
    navigate("/rules-regulation")
  }
  function handleExclusionPolicy() {
    setMenuOpen(false)
    navigate("/exclusion")
  }
  function handleResponsibleGambling() {
    setMenuOpen(false)
    navigate("/responsible-gambling")
  }
  function handlePrivacyPolicy() {
    setMenuOpen(false)
    navigate("/privacy-policy")
  }
  function handleContactUs() {
    setMenuOpen(false)
    navigate("/contact")
  }
  function handleLogoClick() {
    setMenuOpen(false)
    navigate("/")
  }
  function handlePromotion() {
    setMenuOpen(false)
    navigate("/promotion")
  }

  const scrollToSection = (sectionId) => {
    if (window.location.pathname !== "/") {
      navigate("/#" + sectionId)
      return
    }
    const element = document.getElementById(sectionId)
    if (element) {
      element.scrollIntoView({ behavior: "smooth" })
    }
    setMenuOpen(false)
  }

  function onTelegramClick() {
    window.open("https://t.me/YOUR_TELEGRAM_LINK", "_blank")
  }

  useEffect(() => {
    if (accountInfo?.account_balance) {
      sessionStorage.setItem("avl_balance", accountInfo.account_balance)
    }
    if (accountInfo?.service_recharge_option) {
      sessionStorage.setItem("deposit_options", accountInfo.service_recharge_option)
    }
  }, [accountInfo])

  function handleSignOut() {
    setMenuOpen(false)
    sessionStorage.clear()
    navigate("/")
  }

  function handleInviteAndEarn() {
    setMenuOpen(false)
    navigate("/inviteandearn")
  }

  function handleChatWithUs() {
    setMenuOpen(false)
    if (accountInfo?.service_support_url) {
      window.open(accountInfo.service_support_url, "_blank", "noopener,noreferrer")
    } else {
      navigate("/")
    }
  }

  // social links
  const handleExternalLink = (url) => {
    window.open(url, "_blank", "noopener,noreferrer")
  }

  return (
    <>
      <div className="fixed top-0 left-0 w-full z-50 custom-header-wrapper">
        {/* TOPBAR */}
        <div className="topbar" style={{ backgroundColor: COLORS.bg, borderBottom: `1px solid ${COLORS.bg4}` }}>
          <div className="topbar-left">
            <div className="topbar-item"><span style={{ color: COLORS.brand }}>🕐</span> IST {new Date().toLocaleTimeString('en-US', { timeZone: 'Asia/Kolkata', hour12: false, hour: '2-digit', minute: '2-digit' })}</div>
            <div className="live-badge" style={{ backgroundColor: COLORS.red }}>LIVE</div>
            <div className="topbar-item">IPL 2025 — <span className="text-black dark:text-white">CSK vs MI · In Progress</span></div>
            <div className="topbar-item">|</div>
            <div className="topbar-item">Premier League — <span className="text-black dark:text-white">Arsenal vs Chelsea · 67'</span></div>
          </div>
          <div className="topbar-right">
            <div className="topbar-item text-black/70 dark:text-white/70 hover:text-black dark:text-white cursor-pointer transition-colors">🇮🇳 IN</div>
            <div className="topbar-item text-black/70 dark:text-white/70 hover:text-black dark:text-white cursor-pointer transition-colors">🌐 EN</div>
            {accountInfo?.service_support_url && (
              <div 
                className="topbar-item hover:text-black dark:text-white cursor-pointer transition-colors text-black/70 dark:text-white/70"
                onClick={() => handleExternalLink(accountInfo.service_support_url)}
              >
                Support
              </div>
            )}
            <div 
              className="topbar-item hover:text-black dark:text-white cursor-pointer transition-colors text-black/70 dark:text-white/70"
              onClick={handleContactUs}
            >
              Contact
            </div>
          </div>
        </div>

        {/* HEADER */}
        <header className="custom-header" style={{ backgroundColor: `${COLORS.bg}F0`, backdropFilter: "blur(20px)", borderBottom: `1px solid ${COLORS.bg4}` }}>
          <div className="header-inner">
            <div className="custom-logo" onClick={handleLogoClick}>
              <img 
                src={accountInfo?.service_site_logo ? `${BASE_URL}${encodeURI(accountInfo.service_site_logo)}` : ""} 
                className="h-8 w-auto md:h-12 drop-shadow-[0_0_12px_rgba(230,160,0,0.3)] hover:scale-105 transition-transform duration-300" 
                alt={`${accountInfo?.service_site_name || 'Site'} Logo`} 
              />
              {accountInfo?.service_tagline && (
                <div className="logo-sub" style={{ borderLeft: `2px solid ${COLORS.brand}` }} dangerouslySetInnerHTML={{ __html: accountInfo.service_tagline }}></div>
              )}
            </div>

            <nav className="main-nav">
              <button className="nav-link active" onClick={() => scrollToSection("live")} style={{ fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}>Sports</button>
              <button className="nav-link nav-live" onClick={() => scrollToSection("live")} style={{ color: COLORS.red, fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}><span className="dot" style={{ backgroundColor: COLORS.red }}></span>Live</button>
              <button className="nav-link" onClick={() => scrollToSection("casino")} style={{ fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}>Casino</button>
              <button className="nav-link" onClick={() => scrollToSection("slots")} style={{ fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}>Slots</button>
              <button className="nav-link" onClick={() => scrollToSection("aviator")} style={{ fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}>Aviator</button>
              <button className="nav-link" onClick={handlePromotion} style={{ fontFamily: FONTS.head, background: 'none', border: 'none', cursor: 'pointer' }}>Promotions</button>
            </nav>

            <div className="header-cta">
              {!authSecretKey ? (
                <>
                  <button className="btn-outline header-btn" onClick={handleLoginClick} style={{ fontFamily: FONTS.head, borderColor: COLORS.bg4 }}>Log In</button>
                  <button className="btn-primary header-btn" onClick={handleRegisterClick} style={{ background: COLORS.brandGradient, color: '#000', fontFamily: FONTS.head }}>Register</button>
                </>
              ) : (
                <>
                   <button className="btn-outline header-btn" onClick={() => navigate("/deposit")} style={{ fontFamily: FONTS.head, borderColor: COLORS.bg4 }}>Deposit</button>
                  <button className="btn-primary header-btn" onClick={() => navigate("/withdraw")} style={{ background: COLORS.brandGradient, color: '#000', fontFamily: FONTS.head }}>Withdraw</button>
                </>
              )}
              {/* Theme Toggle Button */}
              <button
                className={`ml-2 p-1.5 md:p-2 rounded-xl transition-all duration-300 bg-gray-100 dark:bg-white/5 hover:bg-gray-100 dark:bg-white/10`}
                onClick={toggleTheme}
                title="Toggle Light/Dark Theme"
              >
                {theme === 'dark' ? (
                  <FaSun className="text-xl text-yellow-500" />
                ) : (
                  <FaMoon className="text-xl text-black/90 dark:text-white/90" />
                )}
              </button>
              {/* Mobile Menu Toggle */}
            <button 
              className={`mobile-menu-btn ml-2 md:ml-4 p-1.5 md:p-2 rounded-xl transition-all duration-300 ${menuOpen ? 'bg-gray-100 dark:bg-white/10 rotate-90' : 'bg-gray-100 dark:bg-white/5'}`}
              onClick={() => {
                setMenuOpen(!menuOpen)
                setProfileOpen(false)
              }}
            >
              {menuOpen ? (
                <FaTimes className="text-xl" style={{ color: COLORS.brand }} />
              ) : (
                <FaBars className="text-xl text-black/70 dark:text-white/70" />
              )}
            </button>

            {/* Profile Toggle */}
            {authSecretKey && (
              <button 
                className={`ml-2 p-1.5 md:p-2 rounded-xl transition-all duration-300 ${profileOpen ? 'bg-brand/20' : 'bg-gray-100 dark:bg-white/5'}`}
                onClick={() => {
                  setProfileOpen(!profileOpen)
                  setMenuOpen(false)
                }}
              >
                <FaUserCircle className="text-xl" style={{ color: profileOpen ? COLORS.brand : COLORS.text }} />
              </button>
            )}
            </div>
          </div>
        </header>

        {/* SPORT TABS */}
        <div className="sport-tabs-bar" style={{ backgroundColor: COLORS.bg2, borderBottom: `1px solid ${COLORS.bg4}` }}>
          <div className="sport-tabs-inner">
            {games.map((game, index) => (
              <button
                key={index}
                onClick={() => handleGameSelect(index)}
                className={`sport-tab ${selectedGame === index ? "active" : ""}`}
                style={{ 
                  fontFamily: FONTS.head,
                  color: selectedGame === index ? COLORS.brand : '',
                  borderRight: `1px solid ${COLORS.bg4}`
                }}
              >
                <style>{`
                  .sport-tab.active::after { background: ${COLORS.brand}; transform: scaleX(1); }
                  .sport-tab:hover::after { background: ${COLORS.brand}; }
                `}</style>
                <span className="tab-icon">{game.icon}</span> {game.name}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Spacer div to push content below the fixed navbar */}
      <div className="h-[110px] md:h-[146px] w-full"></div>

      {/* Background Overlay with Blur Effect */}
      {menuOpen && (
        <div
          className="fixed inset-0 bg-black/10 dark:bg-black/50 backdrop-blur-sm transition-all duration-300 z-40"
          onClick={() => setMenuOpen(false)}
        ></div>
      )}

      {/* Profile Sidebar Backdrop */}
      {profileOpen && (
        <div
          className="fixed inset-0 bg-black/10 dark:bg-black/50 backdrop-blur-sm transition-all duration-300 z-40"
          onClick={() => setProfileOpen(false)}
        ></div>
      )}

      {/* Profile Sidebar (Side Pop-up) */}
      <div
        className={`fixed top-0 right-0 h-full w-[85%] md:w-[350px] backdrop-blur-3xl transform ${profileOpen ? "translate-x-0" : "translate-x-full"} transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] z-[60] border-l border-black/5 dark:border-white/5 flex flex-col shadow-2xl`}
        style={{ backgroundColor: `${COLORS.bg2}FB` }}
      >
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-black/5 dark:border-white/5 bg-white/[0.02]">
          <div className="flex items-center gap-3">
            <div className="w-1.5 h-6 rounded-full" style={{ background: COLORS.brandGradient }}></div>
            <h2 className="text-black dark:text-white font-black uppercase tracking-widest text-sm" style={{ fontFamily: FONTS.head }}>Account Details</h2>
          </div>
          <button 
            onClick={() => setProfileOpen(false)} 
            className="p-2 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-100 dark:bg-white/10 text-black/50 dark:text-white/50 transition-all"
          >
            <FaTimes className="w-5 h-5" />
          </button>
        </div>

        {/* User Details */}
        <div className="flex-1 p-8 space-y-8">
          <div className="text-center">
            <div className="w-24 h-24 rounded-3xl mx-auto mb-4 border-2 border-brand/20 p-1">
               <div className="w-full h-full rounded-2xl overflow-hidden bg-brand/10 flex items-center justify-center">
                 <img 
                    src={`https://api.dicebear.com/7.x/initials/svg?seed=${accountInfo?.account_username || 'User'}&backgroundColor=ffad33&bold=true`} 
                    className="w-full h-full object-cover" 
                    alt="Avatar" 
                 />
               </div>
            </div>
            <h3 className="text-lg font-black uppercase tracking-tight" style={{ fontFamily: FONTS.head, color: COLORS.text }}>
              {accountInfo?.account_username || "Guest User"}
            </h3>
            <div className="absolute top-0 right-0 w-2 h-2 rounded-full bg-brand animate-pulse"></div>
          </div>

          <div className="space-y-4">
            {[
              { label: "Profile ID", value: accountInfo?.account_id || "—" },
              { label: "Username", value: accountInfo?.account_username || "—" },
              { label: "Email Address", value: accountInfo?.account_email || "Not Provided" },
              { label: "Mobile Number", value: accountInfo?.account_mobile || "Not Provided" },
            ].map((detail, idx) => (
              <div key={idx} className="rounded-2xl p-3 group transition-all" style={{ backgroundColor: COLORS.bg4, border: `1px solid ${COLORS.bg3}` }}>
                <p className="text-[10px] sm:text-xs font-black uppercase tracking-widest mb-1.5" style={{ color: COLORS.titanium }}>{detail.label}</p>
                <p className="text-sm border-none bg-transparent outline-none w-full font-bold transition-colors" style={{ color: COLORS.text }}>{detail.value}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Footer */}
        <div className="p-6 border-t border-black/5 dark:border-white/5 text-center">
          <button 
            onClick={() => { setProfileOpen(false); navigate("/change-password"); }}
            className="w-full py-4 rounded-xl border border-black/10 dark:border-white/10 text-[10px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 hover:text-black dark:text-white hover:bg-gray-100 dark:bg-white/5 transition-all"
          >
            Manage Security Settings
          </button>
        </div>
      </div>

      {/* Sidebar */}
      <div
        className={`fixed top-0 right-0 h-full w-4/5 md:w-3/5 lg:w-[22%] backdrop-blur-3xl transform ${menuOpen ? "translate-x-0" : "translate-x-full"} transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] z-50 border-l border-black/5 dark:border-white/5 flex flex-col shadow-2xl`}
        style={{ backgroundColor: `${COLORS.bg2}E6` }}
      >
        {/* Sidebar Header */}
        <div className="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/5">
          <div className="flex items-center gap-2">
            <div className="w-2 h-6 rounded-full" style={{ background: COLORS.brandGradient }}></div>
            <h2 className="text-black dark:text-white font-black uppercase tracking-widest text-sm" style={{ fontFamily: FONTS.head }}>{accountInfo?.service_site_name || 'Menu'}</h2>
          </div>
          <button 
            onClick={() => setMenuOpen(false)} 
            className="p-2 rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-red-500/20 hover:text-red-500 text-black/50 dark:text-white/50 transition-all duration-300"
          >
            <FontAwesomeIcon icon={faTimes} className="w-5 h-5" />
          </button>
        </div>

        {/* Sidebar Content */}
        <div className="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-6 space-y-6">
          {/* Account Info Card */}
          <div className="group transition-all duration-500">
             {accountInfo && <AccountInfo accountInfo={accountInfo} />}
          </div>

          <div className="space-y-6">
            {/* Help & Support Section */}
            <div className="space-y-2">
              <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-black/30 dark:text-white/30 pl-2 mb-3" style={{ fontFamily: FONTS.head }}>Service Center</h3>
              <div className="grid grid-cols-1 gap-1.5">
                {[
                  ...(accountInfo?.service_support_url ? [{ icon: <FaHeadset />, label: "Chat With Us", action: handleChatWithUs }] : []),
                  { icon: <FaDownload />, label: "Download APK", action: () => window.open("https://winco.cc/Winco.apk", "_blank") },
                ].map((item, i) => (
                  <div
                    key={i}
                    className="group relative flex items-center p-3 rounded-2xl bg-gray-100 dark:bg-white/2 hover:bg-gray-100 dark:bg-white/5 border border-black/5 dark:border-white/5 transition-all duration-300 cursor-pointer overflow-hidden"
                    onClick={item.action}
                  >
                    <div className="absolute inset-0 bg-gradient-to-r from-brand/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div className="relative w-8 h-8 rounded-lg bg-brand/10 flex items-center justify-center text-brand mr-3 group-hover:scale-110 transition-transform">
                      {item.icon}
                    </div>
                    <span className="relative text-xs font-bold text-black/70 dark:text-white/70 group-hover:text-black dark:text-white transition-colors uppercase tracking-widest" style={{ fontFamily: FONTS.ui }}>{item.label}</span>
                    <div className="ml-auto opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                      <FaStar className="text-[10px] text-brand/40" />
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Statements Section */}
            <div className="space-y-2">
              <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-black/30 dark:text-white/30 pl-2 mb-3" style={{ fontFamily: FONTS.head }}>Financial Activity</h3>
              <div className="grid grid-cols-1 gap-1.5">
                {[
                  { icon: <FaExchangeAlt />, label: "Transactions", action: handleTransactionClick },
                  { icon: <FaHistory />, label: "Betting Profit & Loss", action: handleBettingTransaction },
                ].map((item, i) => (
                  <div
                    key={i}
                    className="group flex items-center p-3 rounded-2xl hover:bg-gray-100 dark:bg-white/5 transition-all duration-300 cursor-pointer border border-transparent hover:border-black/5 dark:border-white/5"
                    onClick={item.action}
                  >
                    <div className="w-8 h-8 rounded-lg bg-brand/5 flex items-center justify-center text-brand/70 mr-3 group-hover:bg-brand/20 group-hover:text-brand transition-all">
                      {item.icon}
                    </div>
                    <span className="text-xs font-bold text-black/60 dark:text-white/60 group-hover:text-black dark:text-white transition-colors uppercase tracking-widest" style={{ fontFamily: FONTS.ui }}>{item.label}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Legal Section */}
            <div className="space-y-2">
              <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-black/30 dark:text-white/30 pl-2 mb-3" style={{ fontFamily: FONTS.head }}>Legal & Compliance</h3>
              <div className="grid grid-cols-2 gap-2">
                {[
                  { icon: <FaGavel />, label: "Rules", action: handleRulesRegulation },
                  { icon: <FaShieldAlt />, label: "Exclusion", action: handleExclusionPolicy },
                  { icon: <FaLock />, label: "Privacy", action: handlePrivacyPolicy },
                  { icon: <FaUserShield />, label: "Responsible", action: handleResponsibleGambling },
                  { icon: <FaEnvelope />, label: "Contact", action: handleContactUs },
                ].map((item, i) => (
                  <div
                    key={i}
                    className="group flex flex-col items-center justify-center p-4 rounded-3xl bg-gray-100 dark:bg-white/2 hover:bg-gray-100 dark:bg-white/5 border border-black/5 dark:border-white/5 transition-all duration-300 cursor-pointer text-center"
                    onClick={item.action}
                  >
                    <div className="text-brand/40 group-hover:text-brand mb-2 text-lg transition-colors">{item.icon}</div>
                    <span className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 group-hover:text-black/80 dark:text-white/80 transition-colors" style={{ fontFamily: FONTS.ui }}>{item.label}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Rewards & Social */}
            <div className="space-y-2">
              <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-black/30 dark:text-white/30 pl-2 mb-3" style={{ fontFamily: FONTS.head }}>Connect & Win</h3>
              <div className="space-y-1.5">
                {[
                  { icon: <FaGift />, label: "Gift Card", action: () => navigate("/gifrcardreedom"), highlight: true },
                  { icon: <FaStar />, label: "Promotions", action: handlePromotion },
                  { icon: <FaShareAlt />, label: "Refer And Earn", action: handleInviteAndEarn },
                ].map((item, i) => (
                  <div
                    key={i}
                    className={`group flex items-center p-3 rounded-2xl transition-all duration-300 cursor-pointer border ${item.highlight ? 'bg-brand/10 border-brand/20' : 'bg-gray-100 dark:bg-white/2 border-black/5 dark:border-white/5 hover:bg-gray-100 dark:bg-white/5'}`}
                    onClick={item.action}
                  >
                    <div className={`w-8 h-8 rounded-lg flex items-center justify-center mr-3 ${item.highlight ? 'bg-brand text-black' : 'bg-gray-100 dark:bg-white/5 text-brand/70'}`}>
                      {item.icon}
                    </div>
                    <span className={`text-xs font-bold uppercase tracking-widest ${item.highlight ? 'text-brand' : 'text-black/60 dark:text-white/60 group-hover:text-black dark:text-white'}`} style={{ fontFamily: FONTS.ui }}>{item.label}</span>
                    {item.highlight && <div className="ml-auto w-1.5 h-1.5 rounded-full bg-brand animate-ping"></div>}
                  </div>
                ))}
              </div>
            </div>

            {/* Account Management */}
            <div className="space-y-4 pt-4 border-t border-black/5 dark:border-white/5">
              <div
                className="group flex items-center p-4 rounded-2xl bg-gray-100 dark:bg-white/2 hover:bg-gray-100 dark:bg-white/5 border border-black/5 dark:border-white/5 transition-all duration-300 cursor-pointer"
                onClick={handleChangePassword}
              >
                <div className="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 mr-4 group-hover:scale-110 transition-transform">
                  <FaKey />
                </div>
                <div className="flex flex-col">
                  <span className="text-xs font-bold text-black dark:text-white uppercase tracking-widest" style={{ fontFamily: FONTS.ui }}>Change Password</span>
                  <span className="text-[9px] text-black/30 dark:text-white/30 uppercase tracking-tighter">Update Account Security</span>
                </div>
              </div>

              <button
                className="w-full flex items-center justify-center gap-3 p-4 rounded-2xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-black dark:text-white transition-all duration-500 border border-red-500/20 group overflow-hidden relative shadow-lg hover:shadow-red-500/20"
                onClick={handleSignOut}
              >
                <div className="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100"></div>
                <FaSignOutAlt className="group-hover:-translate-x-1 transition-transform" />
                <span className="font-black uppercase tracking-[0.2em] text-xs" style={{ fontFamily: FONTS.head }}>Secure Sign Out</span>
              </button>
            </div>
          </div>
        </div>

        {/* Sidebar Footer - Socials */}
        <div className="py-2.5 px-4 bg-black/10 dark:bg-black/40 border-t border-black/5 dark:border-white/5">
          <div className="flex justify-around items-center">
            {(() => {
              if(accountInfo?.service_social_links && accountInfo.service_social_links.length > 0) {
                const iconMap = {
                   'whatsapp': { icon: <FaWhatsapp />, color: "#25D366", prefix: "https://wa.me/" },
                   'telegram': { icon: <FaTelegramPlane />, color: "#0088cc", prefix: "" },
                   'instagram': { icon: <FaInstagram />, color: "#E1306C", prefix: "" },
                   'facebook': { icon: <FaFacebookF />, color: "#1877F2", prefix: "" },
                   'twitter': { icon: <FaTwitter />, color: "#1DA1F2", prefix: "" }
                };
                return accountInfo.service_social_links.map((link, i) => {
                   const platform = link.platform.toLowerCase();
                   let url = link.value;
                   const meta = iconMap[platform] || iconMap['telegram'];
                   if(platform === 'whatsapp' && !url.includes('wa.me')) {
                       url = meta.prefix + url.replace(/\s+/g, '');
                   } else if (!url.startsWith('http')) {
                       url = 'https://' + url;
                   }
                   return (
                     <button
                       key={'dyn_soc'+i}
                       onClick={() => handleExternalLink(url)}
                       className="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-125 group relative"
                       style={{ backgroundColor: `${meta.color}15` }}
                     >
                       <div className="absolute inset-0 rounded-full blur-md opacity-0 group-hover:opacity-50 transition-opacity" style={{ backgroundColor: meta.color }}></div>
                       <span className="relative text-base" style={{ color: meta.color }}>{meta.icon}</span>
                     </button>
                   );
                });
              }
              return null;
            })()}
          </div>
          <p className="text-center text-[7px] text-black/10 dark:text-white/10 uppercase tracking-[0.4em] mt-1 font-bold">{accountInfo?.service_site_name || 'Site'} © 2025</p>
        </div>
      </div>
      {/* Login Modal */}
      {showLogin && (
        <div className="fixed inset-0 flex justify-center items-center z-50">
          <Login
            onClose={closeLoginModal}
            onSwitchToRegister={() => {
              setShowLogin(false)
              setShowRegister(true)
            }}
          />
        </div>
      )}

      {/* Register Modal */}
      {showRegister && (
        <div className="fixed inset-0 flex justify-center items-center z-50">
          <Register
            onClose={closeRegisterModal}
            onSwitchToLogin={() => {
              setShowRegister(false)
              setShowLogin(true)
            }}
          />
        </div>
      )}
    </>
  )
}

export default Navbar

