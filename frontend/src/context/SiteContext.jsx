import React, { createContext, useContext, useState, useEffect } from 'react';
import { API_URL } from '@/utils/constants';

const SiteContext = createContext();

export const SiteProvider = ({ children }) => {
  const [accountInfo, setAccountInfo] = useState(null);
  const [promoBanners, setPromoBanners] = useState([]);
  const [heroBanners, setHeroBanners] = useState([]);
  const [showLogin, setShowLogin] = useState(false);
  const [showRegister, setShowRegister] = useState(false);
  const [loading, setLoading] = useState(true);

  const fetchSiteData = async () => {
    const userId = sessionStorage.getItem("account_id") || "guest";
    const authSecretKey = sessionStorage.getItem("auth_secret_key") || "guest";
    
    try {
      const response = await fetch(API_URL + "?USER_ID=" + userId + "&_t=" + Date.now(), {
        method: "GET",
        cache: "no-store",
        headers: {
          "Content-Type": "application/json",
          Route: "route-account-info",
          AuthToken: authSecretKey,
        },
      });

      const result = await response.json();
      if (result.status_code === "success") {
        setAccountInfo(result.data[0]);
        
        // Normalize Promo Banners
        const rawPromo = result.promo_banners || [];
        const normalizedPromo = rawPromo.map(banner => ({
          image_path: banner.image_path || banner.image || banner.promo_img,
          action_url: banner.action_url || banner.action || banner.promo_action
        }));
        setPromoBanners(normalizedPromo);
        
        // Normalize Hero Banners
        const rawHero = result.hero_banners || result.slideShowList || [];
        const normalizedHero = rawHero.map(banner => ({
          image_path: banner.image_path || banner.slider_img || banner.tbl_slider_img || banner.image,
          action_url: banner.action_url || banner.slider_action || banner.tbl_slider_action || banner.action
        }));
        setHeroBanners(normalizedHero);
      }
    } catch (error) {
      console.error("Error fetching site data:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSiteData();
    
    // Listen for login/logout to refresh data
    const handleStorageChange = (e) => {
      if (e.key === "auth_secret_key" || e.key === "account_id") {
        fetchSiteData();
      }
    };
    window.addEventListener("storage", handleStorageChange);
    return () => window.removeEventListener("storage", handleStorageChange);
  }, []);

  return (
    <SiteContext.Provider value={{ 
      accountInfo, 
      promoBanners, 
      heroBanners,
      loading, 
      refreshSiteData: fetchSiteData,
      showLogin,
      setShowLogin,
      showRegister,
      setShowRegister
    }}>
      {children}
    </SiteContext.Provider>
  );
};

export const useSite = () => {
  const context = useContext(SiteContext);
  if (!context) {
    throw new Error("useSite must be used within a SiteProvider");
  }
  return context;
};
