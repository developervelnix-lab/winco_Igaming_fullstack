import React, { useState, useEffect } from 'react';
import { FaCrown, FaGift, FaTag } from 'react-icons/fa';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';
import { API_URL } from '../../../utils/constants';

const PromotionCard = ({ title, description, endDate, image, onClick }) => {
  const COLORS = useColors();
  const [timeLeft, setTimeLeft] = useState({ days: 0, hours: 0, minutes: 0 });

  useEffect(() => {
    const calculateTimeLeft = () => {
      const difference = new Date(endDate) - new Date();
      if (difference > 0) {
        setTimeLeft({
          days: Math.floor(difference / (1000 * 60 * 60 * 24)),
          hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
          minutes: Math.floor((difference / 1000 / 60) % 60)
        });
      } else {
        setTimeLeft({ days: 0, hours: 0, minutes: 0 });
      }
    };

    calculateTimeLeft();
    const timer = setInterval(calculateTimeLeft, 60000);
    return () => clearInterval(timer);
  }, [endDate]);

  return (
    <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl overflow-hidden group hover:border-brand/20 transition-all duration-300">
      <div className="relative h-28 md:h-32 overflow-hidden">
        <img 
          src={image ? (image.startsWith('http') ? image : `${API_URL.replace('router/', '')}${image}`) : ''} 
          alt={title} 
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        {/* Badge */}
        <div className="absolute top-3 left-3 px-2.5 py-1.5 rounded-lg text-[8px] font-black uppercase tracking-widest border flex items-center gap-1.5"
          style={{ background: `${COLORS.brand}20`, borderColor: `${COLORS.brand}40`, color: COLORS.brand }}>
          <FaTag className="text-[8px]" />
          EXCLUSIVE
        </div>
      </div>
      <div className="p-3 md:p-4">
        <h3 className="text-[10px] md:text-xs font-black text-black/70 dark:text-white/70 uppercase tracking-wide mb-1" style={{ fontFamily: FONTS.head }}>
          {description}
        </h3>
        <h3 className="text-xs md:text-sm font-black text-black dark:text-white uppercase tracking-wide mb-1" style={{ fontFamily: FONTS.head }}>
          {title}
        </h3>
        <p className="text-[9px] font-bold uppercase text-black/30 dark:text-white/30 tracking-widest mb-3">
          Ends in {timeLeft.days}d : {timeLeft.hours}h : {timeLeft.minutes}m
        </p>
        <button 
          onClick={onClick}
          className="w-full px-3 py-2 rounded-lg font-black uppercase tracking-widest text-[9px] text-black dark:text-white shadow-lg active:scale-95 transition-all duration-300 relative overflow-hidden group/btn"
          style={{ background: COLORS.brandGradient, fontFamily: FONTS.ui }}
        >
          <div className="absolute inset-0 bg-gray-100 dark:bg-white/20 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
          <span className="relative">Read More</span>
        </button>
      </div>
    </div>
  );
};

const Promotion = () => {
  const COLORS = useColors();
  const [activeTab, setActiveTab] = useState('all');
  const [promotions, setPromotions] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`${API_URL}?_t=${Date.now()}`, {
      method: "GET",
      headers: { "Route": "route-active-promotions", "AuthToken": "guest", "Content-Type": "application/json" }
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        setPromotions(data.promotions);
      }
      setLoading(false);
    })
    .catch(() => setLoading(false));
  }, []);

  const handleReadMore = (promoId) => {
    console.log(`Clicked on promotion: ${promoId}`);
  };

  const renderPromotions = () => {
    if (loading) {
      return (
        <div className="flex justify-center items-center py-10">
          <div className="w-8 h-8 rounded-full border-2 border-t-brand animate-spin" style={{ borderColor: `${COLORS.brand}40`, borderTopColor: COLORS.brand }}></div>
        </div>
      );
    }
    
    if (promotions.length === 0) {
      return (
        <div className="text-center py-10">
          <i className="bx bx-ghost text-4xl mb-3 opacity-30 dark:text-white text-black"></i>
          <p className="text-[10px] font-bold uppercase tracking-widest text-black/40 dark:text-white/40">No active promotions</p>
        </div>
      );
    }

    const visiblePromotions = promotions.filter(p => activeTab === 'all' || p.category === 'all' || p.category === activeTab);
    
    if (visiblePromotions.length === 0) {
      return (
        <div className="text-center py-10">
          <p className="text-[10px] font-bold uppercase tracking-widest text-black/40 dark:text-white/40">No promotions in this category</p>
        </div>
      );
    }

    return (
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 mt-6">
        {visiblePromotions.map((promo) => (
          <PromotionCard
            key={promo.id}
            title={promo.title}
            description={promo.description}
            endDate={promo.end_date}
            image={promo.image_path}
            onClick={() => handleReadMore(promo.id)}
          />
        ))}
      </div>
    );
  };

  return (
    <div className="w-[96%] max-w-5xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
      style={{ backgroundColor: COLORS.bg2 }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 flex items-center gap-4 relative z-10 bg-white/[0.02]">
        <div className="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-lg"
          style={{ background: COLORS.brandGradient }}>
          <FaCrown />
        </div>
        <div>
          <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Active <span style={{ color: COLORS.brand }}>Promotions</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Exclusive Offers</span>
        </div>
      </div>

      {/* Tabs */}
      <div className="p-4 md:p-5 relative z-10">
        <div className="flex bg-gray-100 dark:bg-black p-1 rounded-xl border border-black/5 dark:border-white/5 max-w-xs mx-auto">
          {['all', 'sports', 'casino'].map((tab) => (
            <button 
              key={tab}
              className={`flex-1 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all duration-300 ${
                activeTab === tab
                  ? 'text-black dark:text-white shadow-lg'
                  : 'text-black/20 dark:text-white/20 hover:text-black/50 dark:text-white/50'
              }`}
              onClick={() => setActiveTab(tab)}
              style={activeTab === tab ? { background: COLORS.brandGradient } : {}}
            >
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </button>
          ))}
        </div>

        {renderPromotions()}
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Promotions</p>
      </div>
    </div>
  );
};

export default Promotion;