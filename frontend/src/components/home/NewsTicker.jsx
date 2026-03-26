import React from 'react';
import { motion } from 'framer-motion';
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';
import { Gamepad2 } from 'lucide-react';
import { useSite } from '../../context/SiteContext';

const NewsTicker = () => {
    const COLORS = useColors();
    const { accountInfo } = useSite();
    
    const scrollingText = accountInfo?.service_marquee 
       ? [{ label: "LATEST NEWS", content: accountInfo.service_marquee, icon: "📢" }]
       : [];
       
    if (scrollingText.length === 0) return null;

    return (
        <div 
            className="w-full h-10 md:h-12 flex items-center overflow-hidden border-y border-black/5 dark:border-white/5 relative z-20"
            style={{ 
                backgroundColor: COLORS.bg2,
                backdropFilter: 'blur(10px)'
            }}
        >
            {/* Title / Icon Sidebar */}
            <div 
                className="absolute left-0 top-0 bottom-0 px-4 md:px-6 flex items-center gap-2 z-40 shadow-[10px_0_20px_-5px_rgba(0,0,0,0.1)] dark:shadow-[10px_0_20px_-5px_rgba(0,0,0,0.5)]"
                style={{ background: COLORS.brandGradient }}
            >
                <Gamepad2 size={16} className="text-white animate-pulse" />
                <span 
                    className="text-[10px] md:text-xs font-black uppercase tracking-widest text-white leading-none whitespace-nowrap"
                    style={{ fontFamily: FONTS.head }}
                >
                    Upcoming Games
                </span>
            </div>

            {/* Scrolling Container */}
            <div className="flex-grow h-full relative overflow-hidden flex items-center">
                <motion.div
                    className="flex whitespace-nowrap items-center"
                    animate={{ x: [0, -2000] }}
                    transition={{
                        x: {
                            repeat: Infinity,
                            repeatType: "loop",
                            duration: 40,
                            ease: "linear",
                        },
                    }}
                >
                    {[1, 2, 3].map((i) => (
                        <div key={i} className="flex items-center">
                            {scrollingText.map((item, idx) => (
                                <div key={idx} className="flex items-center px-8">
                                    <span className="text-[10px] md:text-xs font-black text-brand uppercase tracking-tighter mr-3 px-2 py-0.5 rounded bg-brand/10 border border-brand/20">
                                        {item.icon} {item.label}
                                    </span>
                                    <span 
                                        className="text-[10px] md:text-sm font-bold uppercase tracking-wide text-black/70 dark:text-white/80"
                                        style={{ fontFamily: FONTS.ui }}
                                    >
                                        {item.content}
                                    </span>
                                    <span className="mx-6 text-black/10 dark:text-white/10">|</span>
                                </div>
                            ))}
                        </div>
                    ))}
                </motion.div>
                
                {/* Fade overlays for smooth scrolling into/out of the sidebar */}
                <div className="absolute inset-y-0 left-[120px] md:left-[160px] w-12 pointer-events-none z-20" 
                     style={{ background: `linear-gradient(to right, ${COLORS.bg2}, transparent)` }}></div>
                <div className="absolute inset-y-0 right-0 w-24 pointer-events-none z-20"
                     style={{ background: `linear-gradient(to left, ${COLORS.bg2}, transparent)` }}></div>
            </div>
        </div>
    );
};

export default NewsTicker;
