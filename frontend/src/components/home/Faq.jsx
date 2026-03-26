import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { FaChevronDown, FaQuestionCircle, FaLightbulb } from "react-icons/fa";
import { ranabook } from "../jsondata/info";
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';

const AccordionItem = ({ title, content, isOpen, toggle, index }) => {
  const COLORS = useColors();
  return (
    <motion.div 
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ delay: index * 0.1 }}
      className={`group relative transition-all duration-500 rounded-2xl overflow-hidden border ${
        isOpen ? 'shadow-[0_0_40px_rgba(0,0,0,0.3)]' : 'shadow-lg'
      }`}
      style={{ 
        borderColor: isOpen ? `${COLORS.brand}40` : COLORS.bg4,
        backgroundColor: isOpen ? COLORS.bg2 : COLORS.bg3
      }}
    >
      {/* Light Sweep Effect on Hover */}
      <div className="absolute inset-0 pointer-events-none z-0">
        <div className="absolute top-0 -left-full w-full h-full bg-gradient-to-r from-transparent via-white/5 to-transparent transform -skew-x-12 transition-all duration-1000 group-hover:left-[200%]"></div>
      </div>

      {/* Backdrop Blur */}
      <div className="absolute inset-0 backdrop-blur-2xl -z-10"></div>

      {/* Header */}
      <button
        onClick={toggle}
        className="relative z-10 flex items-center justify-between w-full px-5 md:px-8 py-5 md:py-6 text-left transition-all hover:bg-white/[0.02]"
      >
        <div className="flex items-center gap-4">
          <div 
            className={`w-2 h-2 rounded-full transition-all duration-500 ${isOpen ? 'scale-150' : 'opacity-20'}`}
            style={{ backgroundColor: COLORS.brand, boxShadow: isOpen ? `0 0 10px ${COLORS.brand}` : 'none' }}
          ></div>
          <span 
            className="text-[10px] sm:text-sm md:text-base font-black uppercase tracking-wider transition-colors duration-300 flex-1"
            style={{ color: isOpen ? COLORS.text : COLORS.muted }}
          >
            {title}
          </span>
        </div>
        <motion.div 
          animate={{ rotate: isOpen ? 180 : 0 }}
          className={`p-2 rounded-xl border border-black/5 dark:border-white/5 flex items-center justify-center transition-all duration-300`}
          style={{ 
            backgroundColor: isOpen ? `${COLORS.brand}20` : COLORS.bg4,
            color: isOpen ? COLORS.brand : COLORS.muted
          }}
        >
          <FaChevronDown className="w-4 h-4" />
        </motion.div>
      </button>

      {/* Content */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: 'auto', opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}
          >
            <div className="px-5 md:px-8 pb-5 md:pb-8 flex gap-3 md:gap-4">
              <div className="w-[1.5px] bg-gradient-to-b from-brand/50 to-transparent mt-1"></div>
              <div className="text-[9px] sm:text-xs md:text-base leading-relaxed font-bold uppercase tracking-widest pt-1 md:pt-2" style={{ color: COLORS.titanium }}>
                {content}
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};

const Faq = () => {
  const COLORS = useColors();
  const [openIndex, setOpenIndex] = useState(0);

  const faqs = [
    {
      title: "Why is this one of the best online betting sites in India?",
      content: "This is a trusted betting platform offering fast transactions and secure gaming experiences.",
    },
    {
      title: "Is online betting legal in India?",
      content: "Betting laws vary by state. Always check your local regulations before participating..",
    },
    {
      title: "How do I withdraw my winnings?",
      content: "Withdrawals are processed instantly using our secure banking methods. Minimum withdrawal limits apply.",
    },
    {
      title: "Can I ever win in an online casino?",
      content: "Yes, you can win in an online casino, but outcomes are based on chance, and games are designed with a house edge, so it's important to play responsibly.",
    },
    {
      title: "Is online casino games a skill or luck?",
      content: "Online casino games are generally based on luck, though some games like poker or blackjack may involve elements of skill.",
    },
  ];

  return (
    <section className="relative py-12 md:py-28 overflow-hidden" style={{ backgroundColor: COLORS.bg }}>
      {/* Visual Depth Architecture */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-5xl h-96 bg-brand/5 rounded-full blur-[150px] pointer-events-none opacity-30"></div>
      
      <div className="max-w-4xl mx-auto px-4 md:px-6 relative z-10">
        <div className="flex flex-col items-center mb-10 md:mb-20 text-center">
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 md:gap-3 px-4 md:px-5 py-2 rounded-full bg-white/[0.03] border border-black/10 dark:border-white/10 mb-6 md:mb-8"
          >
             <FaLightbulb className="w-2.5 h-2.5 md:w-3 h-3 text-brand" />
             <span className="text-[8px] md:text-[10px] font-black uppercase tracking-[0.3em] md:tracking-[0.5em] text-black/60 dark:text-white/60">Knowledge Base</span>
          </motion.div>
          
          <motion.h2 
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-3xl sm:text-4xl md:text-6xl font-black text-black dark:text-white mb-6 md:mb-8 uppercase tracking-tighter"
            style={{ fontFamily: FONTS.head || 'Anton, sans-serif' }}
          >
            FREQUENTLY ASKED <span className="inline-block text-transparent bg-clip-text bg-gradient-to-b from-brand to-brand/60" style={{ backgroundImage: `linear-gradient(to bottom, ${COLORS.brandLight || COLORS.brand}, ${COLORS.brand})` }}>QUESTIONS (FAQ)</span>
          </motion.h2>
          
          <motion.div 
            initial={{ width: 0 }}
            whileInView={{ width: 80 }}
            viewport={{ once: true }}
            className="h-1.5 rounded-full"
            style={{ background: COLORS.brandGradient }}
          ></motion.div>
        </div>

        <div className="space-y-3 md:space-y-6">
          {faqs.map((faq, index) => (
            <AccordionItem
              key={index}
              index={index}
              title={faq.title}
              content={faq.content}
              isOpen={openIndex === index}
              toggle={() => setOpenIndex(openIndex === index ? null : index)}
            />
          ))}
        </div>
      </div>

      {/* Digital Stardust Texture */}
      <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-[0.02] pointer-events-none"></div>
    </section>
  );
};

export default Faq;