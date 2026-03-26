import React from 'react';
import { FaUserShield } from 'react-icons/fa';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const ResponsibleGambling = () => {
  const COLORS = useColors();
  const tools = [
    "By selecting a deposit limit per day, week, or month.",
    "By using our 'time out' facility to allow you to suspend your account activity for the following durations - 24 hours, one week, one month, or any other period as you may reasonably request up to a maximum of 6 weeks.",
    "Opting for a self-exclusion, the minimum period being six months, which means your account will be blocked for a set amount of time. Self-exclusions cannot be undone and may only be unlocked by contacting customer services when the self-exclusion time runs out."
  ];

  return (
    <div className="w-[96%] max-w-3xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
      style={{ backgroundColor: COLORS.bg2 }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 flex items-center gap-4 relative z-10 bg-white/[0.02]">
        <div className="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-lg"
          style={{ background: COLORS.brandGradient }}>
          <FaUserShield />
        </div>
        <div>
          <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Responsible <span style={{ color: COLORS.brand }}>Gambling</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Player Welfare</span>
        </div>
      </div>

      {/* Content */}
      <div className="p-4 md:p-5 space-y-4 relative z-10">
        <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5">
          <p className="text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
            The Site is committed to Responsible Gambling and we take our responsibilities towards our customers very seriously. 
            We aim to provide an environment in which you can bet in a safe, fair, and above all responsible manner. If you feel 
            you may have a problem when it comes to controlling your gambling, please consider using one of our tools aimed at 
            helping this:
          </p>
        </div>

        <div className="space-y-3">
          {tools.map((tool, i) => (
            <div key={i} className="flex items-start gap-3 bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5 hover:border-brand/20 transition-all duration-300">
              <div className="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-0.5"
                style={{ background: COLORS.brandGradient, fontFamily: FONTS.head }}>
                {i + 1}
              </div>
              <p className="text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
                {tool}
              </p>
            </div>
          ))}
        </div>
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Responsible Gaming</p>
      </div>
    </div>
  );
};

export default ResponsibleGambling;
