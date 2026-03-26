import React from "react";
import { useNavigate, useLocation } from "react-router-dom";
import sportImage from "./images/sport.webp";
import cusinoImage from "./images/cusino.webp";
import homeImage from "./images/home.webp";
import promotionImage from "./images/Promotion.webp";
import esport from "./images/e-sport.webp"
import { esportfooter } from "../../components/jsondata/esport.js";
import { livecusinofooter } from "../jsondata/livecusiniofooter";
import { API_URL } from "@/utils/constants";
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';

const FooterNav = () => {
  const COLORS = useColors();
  const navigate = useNavigate();
  const location = useLocation();
  const authSecretKey = sessionStorage.getItem("auth_secret_key")
  const userId = sessionStorage.getItem("account_id")
  
  const goToHome = () => {
    navigate("/");
  };

  const isActive = (path) => location.pathname === path;

  // ... (keeping existing handlers handlecusinofooter, handleESportClick) ...
  async function handlecusinofooter(){
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
          GAME_NAME: livecusinofooter["Game Name"],
          GAME_UID: livecusinofooter["Game UID"],
        }),
      });
  
      if (!response.ok) throw new Error("Network response was not ok");
  
      const data = await response.json();
      if (data.data?.game_url) {
        navigate(`/game-url/${encodeURIComponent(data.data.game_url)}/${encodeURIComponent(livecusinofooter["Game Name"])}`);
      }
    } catch (error) {
      console.error("Error logging game click:", error);
    }
  }

  const handleESportClick = async () => {
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
          GAME_NAME: esportfooter["Game Name"],
          GAME_UID: esportfooter["Game UID"],
        }),
      });
  
      if (!response.ok) throw new Error("Network response was not ok");
  
      const data = await response.json();
      if (data.data?.game_url) {
        navigate(`/game-url/${encodeURIComponent(data.data.game_url)}/${encodeURIComponent(esportfooter["Game Name"])}`);
      }
    } catch (error) {
      console.error("Error logging game click:", error);
    }
  };

  const scrollToSection = (id) => {
    if (location.pathname === "/") {
      const element = document.getElementById(id);
      if (element) {
        element.scrollIntoView({ behavior: "smooth" });
      }
    } else {
      navigate(`/#${id}`);
    }
  };

  const navItems = [
    { label: "Sport", icon: sportImage, active: false, action: () => scrollToSection("live") },
    { label: "E-Sport", icon: esport, active: false, action: handleESportClick },
    { label: "Home", icon: homeImage, isCenter: true, action: goToHome },
    { label: "Casino", icon: cusinoImage, active: false, action: () => scrollToSection("slots-trending") },
    { label: "Promo", icon: promotionImage, active: isActive("/promotion"), action: () => scrollToSection("promotions") },
  ];

  return (
    <div className="fixed bottom-0 left-0 w-full z-50 px-4 pb-4 md:hidden">
      <div 
        className="w-full h-16 rounded-[2rem] flex justify-around items-center border border-black/5 dark:border-white/5 shadow-[0_-10px_40px_rgba(0,0,0,0.5)] relative group/nav"
        style={{ 
          backgroundColor: `${COLORS.bg2}E0`,
          backdropFilter: "blur(20px)"
        }}
      >
        {/* Glow Effects */}
        <div className="absolute inset-0 bg-gradient-to-t from-brand/5 to-transparent opacity-50 rounded-[2rem] pointer-events-none"></div>
        
        {navItems.map((item, i) => (
          <div 
            key={i} 
            className={`flex flex-col items-center justify-center transition-all duration-300 relative ${item.isCenter ? '-mt-8 scale-110 z-10' : 'flex-1'}`}
            onClick={item.action}
          >
            {item.isCenter ? (
              <div className="flex flex-col items-center group/home">
                <div className="absolute -inset-4 bg-brand/20 blur-2xl rounded-full opacity-0 group-hover/home:opacity-100 transition-opacity pointer-events-none"></div>
                <div className="w-14 h-14 rounded-full bg-gradient-to-br from-brand to-brandDark p-1 shadow-[0_0_25px_rgba(230,160,0,0.4)] relative">
                  <div className="w-full h-full rounded-full bg-black/10 dark:bg-black/40 flex items-center justify-center overflow-hidden">
                    <img src={item.icon} alt={item.label} className="w-8 h-8 object-contain drop-shadow-lg" />
                  </div>
                </div>
                <span 
                  className="mt-1.5 text-[9px] font-black uppercase tracking-widest text-black dark:text-white shadow-sm" 
                  style={{ fontFamily: FONTS.head, textShadow: '0 2px 4px rgba(0,0,0,0.5)' }}
                >
                  {item.label}
                </span>
              </div>
            ) : (
              <div className="flex flex-col items-center group/item">
                <div className={`w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 ${item.active ? 'bg-brand/20 border-brand/40' : 'bg-gray-100 dark:bg-white/5 border-black/5 dark:border-white/5'} border mb-1`}>
                   <img 
                    src={item.icon} 
                    alt={item.label} 
                    className={`w-6 h-6 object-contain rounded-lg transition-all duration-300 ${item.active ? 'opacity-100 scale-110' : 'opacity-40 group-hover/item:opacity-70 group-hover/item:scale-105'}`} 
                  />
                </div>
                <span className={`text-[8px] font-bold uppercase tracking-widest transition-colors duration-300 ${item.active ? 'text-brand' : 'text-black/40 dark:text-white/40 group-hover/item:text-black/70 dark:text-white/70'}`} style={{ fontFamily: FONTS.ui }}>{item.label}</span>
                {item.active && <div className="absolute -bottom-1 w-1 h-1 rounded-full bg-brand shadow-[0_0_8px_brand]"></div>}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default FooterNav;