import React, { useRef } from 'react';
import { motion, useMotionValue, useTransform } from 'framer-motion';
import { FaRegClock, FaWallet, FaUserPlus, FaRegCheckCircle } from 'react-icons/fa';
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';
import { ranabook } from '../jsondata/info';

const FeatureCard = ({ feature, index }) => {
  const COLORS = useColors();
  const ref = useRef(null);
  const x = useMotionValue(0);
  const y = useMotionValue(0);
  const rotateX = useTransform(y, [-60, 60], [8, -8]);
  const rotateY = useTransform(x, [-60, 60], [-8, 8]);

  const handleMouseMove = (e) => {
    const rect = ref.current.getBoundingClientRect();
    x.set(e.clientX - rect.left - rect.width / 2);
    y.set(e.clientY - rect.top - rect.height / 2);
  };

  const handleMouseLeave = () => {
    x.set(0);
    y.set(0);
  };

  const gradients = [
    'from-violet-500/20 via-purple-500/10 to-transparent',
    'from-cyan-500/20 via-blue-500/10 to-transparent',
    'from-emerald-500/20 via-teal-500/10 to-transparent',
    'from-amber-500/20 via-orange-500/10 to-transparent',
  ];

  const glows = [
    'rgba(139,92,246,0.6)',
    'rgba(6,182,212,0.6)',
    'rgba(16,185,129,0.6)',
    'rgba(245,158,11,0.6)',
  ];

  const iconColors = [
    '#a78bfa',
    '#22d3ee',
    '#34d399',
    '#fbbf24',
  ];

  return (
    <motion.div
      ref={ref}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      style={{ rotateX, rotateY, transformStyle: 'preserve-3d', perspective: 800 }}
      initial={{ opacity: 0, y: 40 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.7, delay: index * 0.12, ease: [0.22, 1, 0.36, 1] }}
      className="group relative rounded-3xl p-[1px] cursor-default"
    >
      {/* Animated border gradient */}
      <div
        className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
        style={{
          background: `conic-gradient(from 180deg, ${glows[index]}, transparent 60%, ${glows[index]})`,
        }}
      />

      {/* Card body */}
      <div
        className={`relative rounded-3xl p-4 sm:p-5 md:p-9 flex flex-col items-center text-center h-full overflow-hidden`}
        style={{ backgroundColor: `${COLORS.bg2 || '#0f0f17'}`, border: '1px solid rgba(255,255,255,0.06)' }}
      >
        {/* Gradient wash on hover */}
        <div className={`absolute inset-0 bg-gradient-to-br ${gradients[index]} opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl`} />

        {/* Corner shimmer */}
        <div className="absolute -top-10 -right-10 w-32 h-32 rounded-full blur-3xl opacity-0 group-hover:opacity-40 transition-opacity duration-700"
          style={{ backgroundColor: glows[index] }} />

        {/* Icon */}
        <div className="relative z-10 mb-5 md:mb-7">
          <div
            className="w-12 h-12 md:w-20 md:h-20 rounded-2xl flex items-center justify-center text-2xl md:text-4xl transition-transform duration-500 group-hover:scale-110"
            style={{
              background: `linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02))`,
              border: `1px solid rgba(255,255,255,0.07)`,
              boxShadow: `0 0 0 0 ${glows[index]}`,
            }}
          >
            <motion.div
              animate={{ rotate: [0, 5, -5, 0] }}
              transition={{ duration: 4, repeat: Infinity, delay: index * 0.5 }}
              style={{ color: iconColors[index], filter: `drop-shadow(0 0 12px ${glows[index]})` }}
            >
              {feature.icon}
            </motion.div>
          </div>

          {/* Pulse ring */}
          <div
            className="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-ping-slow"
            style={{ border: `1px solid ${iconColors[index]}40` }}
          />
        </div>

        {/* Number badge */}
        <div
          className="absolute top-4 right-4 text-[10px] font-black tracking-widest opacity-20 group-hover:opacity-60 transition-opacity duration-300"
          style={{ fontFamily: FONTS.head, color: iconColors[index] }}
        >
          0{index + 1}
        </div>

        {/* Title */}
        <h3
          className="relative z-10 text-[9px] sm:text-[10px] md:text-base font-black uppercase tracking-[0.1em] sm:tracking-[0.18em] text-black/50 dark:text-white/50 group-hover:text-black dark:text-white transition-colors duration-400 leading-tight"
          style={{ fontFamily: FONTS.head }}
        >
          {feature.title}
        </h3>

        {/* Bottom accent line */}
        <div
          className="relative z-10 mt-5 h-0.5 w-0 group-hover:w-10 rounded-full transition-all duration-500"
          style={{ background: `linear-gradient(90deg, ${iconColors[index]}, transparent)` }}
        />
      </div>
    </motion.div>
  );
};

const FeaturesSection = () => {
  const COLORS = useColors();
  const features = [
    { title: 'Fast Withdrawal', icon: <FaRegClock /> },
    { title: 'Instant Deposit', icon: <FaWallet /> },
    { title: '1-Click Signup', icon: <FaUserPlus /> },
    { title: 'Trusted Platform', icon: <FaRegCheckCircle /> },
  ];

  return (
    <section className="relative py-12 md:py-24 overflow-hidden" style={{ backgroundColor: COLORS.bg }}>

      {/* Background grid lines */}
      <div className="absolute inset-0 opacity-[0.03]"
        style={{
          backgroundImage: 'linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px)',
          backgroundSize: '60px 60px'
        }}
      />

      {/* Central glow blob */}
      <div
        className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[300px] rounded-full blur-[140px] pointer-events-none opacity-20"
        style={{ background: `radial-gradient(ellipse, ${COLORS.brand}, transparent 70%)` }}
      />

      <div className="max-w-7xl mx-auto px-6 relative z-10">

        {/* Header */}
        <div className="text-center mb-10 md:mb-20">
          <motion.p
            initial={{ opacity: 0, y: 10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-[8px] md:text-xs font-black uppercase tracking-[0.2em] md:tracking-[0.4em] mb-3 md:mb-5"
            style={{ color: COLORS.brand, fontFamily: FONTS.ui }}
          >
            Why We're Different
          </motion.p>

          <motion.h2
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="text-3xl sm:text-4xl md:text-6xl font-black text-black dark:text-white uppercase tracking-tight leading-none mb-4 md:mb-6"
            style={{ fontFamily: FONTS.head }}
          >
            Why Choose{' '}
            <span
              className="relative inline-block"
              style={{
                background: COLORS.brandGradient,
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text',
              }}
            >
              {ranabook?.name || 'Site'}
            </span>
            ?
          </motion.h2>

          <motion.div
            initial={{ scaleX: 0 }}
            whileInView={{ scaleX: 1 }}
            viewport={{ once: true }}
            transition={{ delay: 0.3, duration: 0.6 }}
            className="h-[2px] w-20 mx-auto rounded-full origin-center"
            style={{ background: COLORS.brandGradient }}
          />
        </div>

        {/* Cards */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4 md:gap-6" style={{ perspective: '1000px' }}>
          {features.map((feature, index) => (
            <FeatureCard key={index} feature={feature} index={index} />
          ))}
        </div>
      </div>
    </section>
  );
};

export default FeaturesSection;