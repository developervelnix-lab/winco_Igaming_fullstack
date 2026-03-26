import React, { useState } from "react";
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import { FaChevronLeft, FaChevronRight } from "react-icons/fa";
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';

// Import all provider images
import bf from './providers/bf.png';
import bggaming from './providers/bggaming.png';
import bigtiminggaming from './providers/bigtiminggaming.png';
import caleta from './providers/Caleta.png';
import boominggames from './providers/boominggames.png';
import booongo from './providers/Booongo.png';
import cq9 from './providers/CQ9.png';
import endorphina from './providers/endorphina.png';
import evolution from './providers/Evolution.png';
import evoplay from './providers/Evoplay.png';
import gameart from './providers/gameart.png';
import pgsoft from './providers/PGSOFT.png';
import playngo from './providers/PlayNGo.png';
import playson from './providers/playson.png';
import playtech from './providers/PlayTech.png';
import pragmaticplay from './providers/PragmaticPlay.png';
import relaxgaming from './providers/relaxgaming.png';
import redtiger from './providers/redtiger.png';
import saba from './providers/sabalogo.png';
import skywind from './providers/Skywind.png';
import v8 from './providers/V8.png';
import yesgaming from './providers/yesgaming.png';
import rubyplay from './providers/RubyPlay.png';

const providersData = [
  { logo: bf, name: "BF Games" },
  { logo: bggaming, name: "BG Gaming" },
  { logo: bigtiminggaming, name: "BTG" },
  { logo: boominggames, name: "Booming" },
  { logo: booongo, name: "Booongo" },
  { logo: caleta, name: "Caleta" },
  { logo: cq9, name: "CQ9" },
  { logo: endorphina, name: "Endorphina" },
  { logo: evolution, name: "Evolution" },
  { logo: evoplay, name: "Evoplay" },
  { logo: gameart, name: "GameArt" },
  { logo: pgsoft, name: "PG Soft" },
  { logo: playngo, name: "Play'n GO" },
  { logo: playson, name: "Playson" },
  { logo: playtech, name: "Playtech" },
  { logo: pragmaticplay, name: "Pragmatic" },
  { logo: relaxgaming, name: "Relax" },
  { logo: redtiger, name: "Red Tiger" },
  { logo: saba, name: "Saba" },
  { logo: skywind, name: "Skywind" },
  { logo: v8, name: "V8" },
  { logo: yesgaming, name: "Yes Gaming" },
  { logo: rubyplay, name: "Ruby Play" }
];

const GameProvider = () => {
  const COLORS = useColors();
  const [showAll, setShowAll] = useState(false);

  return (
    <section className="relative py-4 md:py-12 overflow-hidden" style={{ backgroundColor: COLORS.bg }}>
      {/* Cinematic Background Atmosphere */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-7xl h-64 bg-brand/5 rounded-full blur-[120px] pointer-events-none"></div>

      <div className="max-w-7xl mx-auto px-6">
        {/* Elite Header */}
        <div className="flex flex-col md:flex-row justify-between md:items-end gap-5 md:gap-0 mb-8 md:mb-10 pb-6 border-b border-black/5 dark:border-white/5">
          <div className="flex items-center gap-4 md:gap-6">
            <div 
              className="h-8 md:h-10 w-1.5 rounded-full"
              style={{ background: COLORS.brandGradient }}
            ></div>
            <div>
              <h2 
                className="text-xl sm:text-2xl md:text-3xl font-black text-black dark:text-white tracking-[0.1em] md:tracking-[0.2em] uppercase leading-none"
                style={{ fontFamily: FONTS.head }}
              >
                Game <span style={{ color: COLORS.brand }}>Providers</span>
              </h2>
              <p className="text-[8px] sm:text-[9px] md:text-[10px] text-black/30 dark:text-white/30 font-bold uppercase tracking-[0.2em] md:tracking-[0.4em] mt-2 md:mt-3">Worldwide Partnerships</p>
            </div>
          </div>

          <button
            onClick={() => setShowAll(!showAll)}
            className="w-full md:w-auto px-4 md:px-6 py-3 md:py-2 rounded-xl border border-black/10 dark:border-white/10 text-[10px] font-black uppercase tracking-[0.2em] text-black/60 dark:text-white/60 hover:text-black dark:text-white hover:border-brand/40 transition-all duration-300 backdrop-blur-md text-center"
          >
            {showAll ? "Switch to Carousel" : "View All Partners"}
          </button>
        </div>

        {/* Dynamic Display Area */}
        {showAll ? (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
            {providersData.map((provider, index) => (
              <ProviderCard key={index} logo={provider.logo} name={provider.name} />
            ))}
          </div>
        ) : (
          <div className="relative">
            <Swiper
              modules={[Autoplay, Navigation]}
              spaceBetween={8}
              slidesPerView={3}
              autoplay={{
                delay: 3000,
                disableOnInteraction: false,
              }}
              navigation={{
                prevEl: '.provider-prev',
                nextEl: '.provider-next',
              }}
              breakpoints={{
                640: { slidesPerView: 3 },
                1024: { slidesPerView: 5 },
                1280: { slidesPerView: 6 },
              }}
              className="w-full"
            >
              {providersData.map((provider, index) => (
                <SwiperSlide key={index}>
                  <ProviderCard logo={provider.logo} name={provider.name} />
                </SwiperSlide>
              ))}
            </Swiper>

            {/* Custom Navigation Controls - Re-positioned inside to avoid cutting */}
            <button className="provider-prev absolute left-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-black/10 dark:bg-black/60 border border-black/10 dark:border-white/10 flex items-center justify-center text-black/40 dark:text-white/40 hover:text-brand hover:border-brand/50 transition-all opacity-0 group-hover/swiper:opacity-100 backdrop-blur-md">
              <FaChevronLeft size={12} />
            </button>
            <button className="provider-next absolute right-0 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-black/10 dark:bg-black/60 border border-black/10 dark:border-white/10 flex items-center justify-center text-black/40 dark:text-white/40 hover:text-brand hover:border-brand/50 transition-all opacity-0 group-hover/swiper:opacity-100 backdrop-blur-md">
              <FaChevronRight size={12} />
            </button>
          </div>
        )}
      </div>
    </section>
  );
};

const ProviderCard = ({ logo, name }) => {
  return (
    <div className="group relative w-full aspect-[3/2] rounded-2xl p-[1px] bg-gradient-to-br from-white/20 to-transparent transition-all duration-500 hover:from-brand/50 shadow-xl">
      <div className="w-full h-full rounded-[15px] bg-white dark:bg-[#121212] flex flex-col items-center justify-center p-4 overflow-hidden relative border border-black/5 dark:border-white/5">
        {/* Stronger Pure White Backing for Black Logos */}
        <div className="absolute inset-0 bg-white opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-500"></div>
        <div 
          className="absolute w-3/4 h-3/4 rounded-full blur-2xl opacity-60 pointer-events-none"
          style={{ background: 'radial-gradient(circle, white 0%, transparent 70%)' }}
        ></div>
        
        <img
          src={logo}
          alt={name}
          className="relative z-10 max-w-[85%] max-h-[70%] object-contain transition-all duration-500 scale-100 group-hover:scale-110"
          onError={(e) => {
            e.target.onerror = null;
            e.target.src = "https://via.placeholder.com/150?text=Logo";
          }}
        />
        
        {/* Subtle Brand Tag */}
        <span className="absolute bottom-2 sm:bottom-3 text-[5px] sm:text-[7px] font-black uppercase tracking-[0.1em] sm:tracking-[0.3em] text-black/20 dark:text-white/20 group-hover:text-brand transition-all duration-300 whitespace-nowrap">
          Official Partner
        </span>
      </div>
    </div>
  );
};

export default GameProvider;