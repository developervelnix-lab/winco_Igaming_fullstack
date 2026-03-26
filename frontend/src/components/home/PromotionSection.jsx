import React from 'react';
import { motion } from "framer-motion";
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Pagination } from 'swiper/modules';
import {
  FaGift,
  FaCrown,
  FaStar,
  FaTrophy,
  FaGamepad,
  FaGem,
  FaBolt,
  FaCalendarAlt,
  FaShieldAlt,
  FaUserFriends,
  FaArrowRight
} from 'react-icons/fa';
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';
import { URL as BASE_URL } from "../../utils/constants"

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';

function PromotionSection({ banners = [] }) {
  const COLORS = useColors();
  const promotions = [
    {
      title: "Welcome Bonus",
      description: "Get 100% bonus on your first deposit!",
      buttonText: "Claim Now",
      icon: <FaGift className="text-brand" size={24} />,
      gradient: "from-yellow-500/20 to-orange-500/20"
    },
    {
      title: "VIP Rewards",
      description: "Exclusive odds boost for VIP members",
      buttonText: "Join VIP",
      icon: <FaCrown className="text-yellow-400" size={24} />,
      gradient: "from-indigo-500/20 to-blue-500/20"
    },
    {
      title: "Free Bet",
      description: "Place 5 bets and get 1 free bet!",
      buttonText: "Get Started",
      icon: <FaStar className="text-blue-400" size={24} />,
      gradient: "from-purple-500/20 to-pink-500/20"
    },
    {
      title: "Parlay Boost",
      description: "10% extra on winning parlays",
      buttonText: "Bet Now",
      icon: <FaTrophy className="text-amber-400" size={24} />,
      gradient: "from-teal-500/20 to-green-500/20"
    },
    {
      title: "Sports Special",
      description: "Double winnings on underdog victories",
      buttonText: "See Offers",
      icon: <FaGamepad className="text-emerald-400" size={24} />,
      gradient: "from-orange-500/20 to-red-500/20"
    },
    {
      title: "Loyalty Bonus",
      description: "Earn points & redeem exclusive rewards!",
      buttonText: "Start Earning",
      icon: <FaGem className="text-cyan-400" size={24} />,
      gradient: "from-pink-500/20 to-red-400/20"
    },
    {
      title: "Flash Bet Offer",
      description: "Place a bet in the next 10 mins & get 5% cashback!",
      buttonText: "Bet Fast",
      icon: <FaBolt className="text-yellow-300" size={24} />,
      gradient: "from-blue-600/20 to-purple-600/20"
    },
    {
      title: "Weekend Special",
      description: "Double rewards every Saturday & Sunday!",
      buttonText: "Check Now",
      icon: <FaCalendarAlt className="text-rose-400" size={24} />,
      gradient: "from-orange-400/20 to-pink-500/20"
    },
    {
      title: "Risk-Free Bet",
      description: "Get your first bet back if you lose!",
      buttonText: "Try Now",
      icon: <FaShieldAlt className="text-slate-400" size={24} />,
      gradient: `from-[${COLORS.brandLight}]20 to-[${COLORS.brandDark}]20`
    },
    {
      title: "Refer & Earn",
      description: "Invite friends & get ₹500 bonus each!",
      buttonText: "Refer Now",
      icon: <FaUserFriends className="text-brand" size={24} />,
      gradient: `from-[${COLORS.brand}]20 to-[${COLORS.brandDark}]20`
    }
  ];

  return (
    <section className="relative py-6 md:py-16 overflow-hidden" style={{ backgroundColor: COLORS.bg }}>
      {/* Cinematic Background Elements */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
      <div className="absolute -top-24 left-1/4 w-96 h-96 bg-brand/5 rounded-full blur-[120px] pointer-events-none"></div>
      <div className="absolute top-1/2 right-1/4 w-96 h-96 bg-brand/10 rounded-full blur-[150px] pointer-events-none"></div>

      <div className="max-w-7xl mx-auto px-4 md:px-6">
        {/* Elite Header */}
        <div className="flex items-center gap-4 md:gap-6 mb-8 md:mb-12">
          <div
            className="h-10 md:h-12 w-1.5 rounded-full"
            style={{ background: COLORS.brandGradient }}
          ></div>
          <div>
            <h2
              className="text-2xl sm:text-3xl md:text-4xl font-black text-black dark:text-white tracking-[0.1em] md:tracking-[0.2em] uppercase leading-none"
              style={{ fontFamily: FONTS.head }}
            >
              Current <span style={{ color: COLORS.brand }}>Promotions</span>
            </h2>
            <div className="flex items-center gap-2 md:gap-3 mt-2 md:mt-3">
              <div className="h-px w-8 md:w-12 bg-gray-100 dark:bg-white/20"></div>
              <span className="text-[8px] md:text-[10px] text-black/40 dark:text-white/40 font-bold uppercase tracking-[0.2em] md:tracking-[0.4em]">Exclusive Elite Offers</span>
            </div>
          </div>
        </div>

        {/* Promotions Carousel */}
        <div className="relative group">
          <Swiper
            modules={[Autoplay, Pagination]}
            spaceBetween={24}
            slidesPerView={1}
            autoplay={{
              delay: 4000,
              disableOnInteraction: false,
            }}
            breakpoints={banners.length > 0 ? {
              0: { slidesPerView: 1, spaceBetween: 12 },
              640: { slidesPerView: 1, spaceBetween: 20 },
              1024: { slidesPerView: 1, spaceBetween: 24 },
            } : {
              0: { slidesPerView: 1.15, spaceBetween: 12 },
              480: { slidesPerView: 1.8, spaceBetween: 16 },
              640: { slidesPerView: 2, spaceBetween: 20 },
              1024: { slidesPerView: 3, spaceBetween: 24 },
              1280: { slidesPerView: 4, spaceBetween: 24 },
            }}
            className="!pb-12"
          >
            {banners.length > 0 ? (
              banners.map((item, index) => (
                <SwiperSlide key={index}>
                  <motion.div
                     whileHover={{ scale: 1.01 }}
                     className="relative aspect-[21/9] md:aspect-[3/1] rounded-2xl md:rounded-3xl overflow-hidden cursor-pointer"
                     onClick={() => item.action_url && window.open(item.action_url, '_blank')}
                  >
                    <img 
                      src={`${BASE_URL}${encodeURI(item.image_path)}`} 
                      className="w-full h-full object-cover" 
                      alt={`Promotion ${index + 1}`} 
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                  </motion.div>
                </SwiperSlide>
              ))
            ) : (
              promotions.map((promo, index) => (
              <SwiperSlide key={index}>
                <motion.div
                  whileHover={{ y: -8 }}
                  className="relative group h-full"
                >
                  <div className={`min-h-[220px] md:min-h-[280px] rounded-2xl md:rounded-3xl p-5 md:p-8 backdrop-blur-3xl border border-black/10 dark:border-white/10 shadow-2xl flex flex-col justify-between transition-all duration-500 bg-gradient-to-br ${promo.gradient} hover:border-black/20 dark:border-white/20`}>
                    {/* Glass Reflection */}
                    <div className="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl pointer-events-none"></div>

                    <div>
                      <div className="flex justify-between items-start mb-6">
                        <div className="p-3 bg-gray-100 dark:bg-white/5 rounded-2xl border border-black/10 dark:border-white/10 group-hover:scale-110 transition-transform duration-500">
                          {promo.icon}
                        </div>
                        <span className="text-[8px] font-black uppercase tracking-[0.3em] text-black/30 dark:text-white/30 group-hover:text-brand transition-colors duration-300 px-2 py-1 border border-black/5 dark:border-white/5 rounded-full">
                          Featured
                        </span>
                      </div>

                      <h3
                        className="text-xl font-black text-black dark:text-white mb-2 uppercase tracking-wide leading-tight line-clamp-1"
                        style={{ fontFamily: FONTS.head }}
                      >
                        {promo.title}
                      </h3>
                      <p className="text-xs text-black/50 dark:text-white/50 font-medium leading-relaxed mb-6 line-clamp-2">
                        {promo.description}
                      </p>
                    </div>

                    <div className="flex items-center justify-between mt-auto pt-4 border-t border-black/5 dark:border-white/5">
                      <button
                        className="text-[10px] font-black uppercase tracking-[0.2em] text-black dark:text-white group-hover:text-brand transition-colors duration-300 flex items-center gap-2"
                      >
                        {promo.buttonText}
                        <FaArrowRight size={10} className="group-hover:translate-x-1 transition-transform" />
                      </button>
                    </div>
                  </div>
                </motion.div>
              </SwiperSlide>
            ))
            )}
          </Swiper>
        </div>
      </div>
    </section>
  );
}

export default PromotionSection;
