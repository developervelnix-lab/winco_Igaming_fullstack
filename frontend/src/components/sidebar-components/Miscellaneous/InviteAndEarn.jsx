import React, { useState, useEffect } from 'react';
import { FaCopy, FaShareAlt, FaGift, FaCheckCircle } from 'react-icons/fa';
import { URL } from '@/utils/constants';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const InviteAndEarn = () => {
  const COLORS = useColors();
  const referralCode = sessionStorage.getItem('account_id');
  const referralURL = `${URL}home?referralcode=${referralCode}`;
  const [showToast, setShowToast] = useState(false);

  const handleCopy = (text) => {
    navigator.clipboard.writeText(text);
    setShowToast(true);
  };

  useEffect(() => {
    if (showToast) {
      const timer = setTimeout(() => setShowToast(false), 2500);
      return () => clearTimeout(timer);
    }
  }, [showToast]);

  return (
    <div className="w-[96%] max-w-2xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
      style={{ backgroundColor: COLORS.bg2 }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Toast */}
      {showToast && (
        <div className="fixed top-6 left-1/2 -translate-x-1/2 z-[100] animate-fadeIn">
          <div className="flex items-center p-4 rounded-xl border bg-black/10 dark:bg-black/90 border-green-500/30 text-black dark:text-white shadow-2xl backdrop-blur-xl">
            <FaCheckCircle className="text-green-500 mr-3 text-xl" />
            <span className="text-xs font-black uppercase tracking-wide">Copied to clipboard!</span>
          </div>
        </div>
      )}

      {/* Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 text-center relative z-10 bg-white/[0.02]">
        <div className="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-xl mx-auto mb-3"
          style={{ background: COLORS.brandGradient }}>
          <FaGift />
        </div>
        <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
          Refer Friends & <span style={{ color: COLORS.brand }}>Earn</span>
        </h2>
        <p className="text-[10px] text-black/40 dark:text-white/40 mt-1.5 max-w-xs mx-auto" style={{ fontFamily: FONTS.ui }}>
          Share your referral code and earn <span className="font-black" style={{ color: COLORS.brand }}>20%</span> of every friend's deposit!
        </p>
      </div>

      {/* Content */}
      <div className="p-4 md:p-5 space-y-4 relative z-10">

        {/* Earnings Card */}
        <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-3 flex items-center justify-center gap-3">
          <div className="w-8 h-8 rounded-lg flex items-center justify-center text-base"
            style={{ background: `${COLORS.brand}15`, color: COLORS.brand }}>
            <FaGift />
          </div>
          <div>
            <span className="text-[8px] font-black uppercase tracking-widest text-black/30 dark:text-white/30 block">Your Earnings</span>
            <span className="text-lg font-black text-black dark:text-white" style={{ fontFamily: FONTS.head }}>₹0.00</span>
          </div>
        </div>

        {/* Referral Link */}
        <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 hover:border-brand/20 transition-all duration-300">
          <div className="flex items-center gap-2 mb-2">
            <FaShareAlt className="text-[10px]" style={{ color: COLORS.brand }} />
            <span className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40" style={{ fontFamily: FONTS.ui }}>Your Referral Link</span>
          </div>
          <div className="flex flex-col md:flex-row gap-2">
            <input
              type="text"
              value={referralURL}
              readOnly
              className="flex-1 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2 text-[10px] focus:outline-none shadow-inner truncate transition-all text-black dark:text-white"
              style={{ fontFamily: FONTS.ui }}
            />
            <button
              onClick={() => handleCopy(referralURL)}
              className="flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg font-black uppercase tracking-widest text-[9px] text-black dark:text-white shadow-lg active:scale-95 transition-all duration-300 relative overflow-hidden group whitespace-nowrap"
              style={{ background: COLORS.brandGradient, fontFamily: FONTS.ui }}
            >
              <div className="absolute inset-0 bg-gray-100 dark:bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <FaCopy className="relative" /> <span className="relative">Copy</span>
            </button>
          </div>
        </div>

        {/* Referral Code */}
        <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 hover:border-brand/20 transition-all duration-300">
          <div className="flex items-center gap-2 mb-2">
            <span className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40" style={{ fontFamily: FONTS.ui }}>Your Unique Code</span>
          </div>
          <div className="flex flex-col md:flex-row gap-2">
            <div 
              className="flex-1 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2 text-center md:text-left shadow-inner transition-all text-black dark:text-white"
            >
              <span className="font-mono text-base font-black tracking-[0.3em]" style={{ fontFamily: FONTS.head }}>
                {referralCode}
              </span>
            </div>
            <button
              onClick={() => handleCopy(referralCode)}
              className="flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg font-black uppercase tracking-widest text-[9px] bg-gray-100 dark:bg-white/5 text-black/50 dark:text-white/50 hover:text-black dark:text-white hover:bg-gray-100 dark:bg-white/10 transition-all border border-black/5 dark:border-white/5 whitespace-nowrap"
              style={{ fontFamily: FONTS.ui }}
            >
              <FaCopy /> Copy
            </button>
          </div>
        </div>

        {/* How It Works */}
        <div className="flex items-center justify-center gap-3 py-4">
          {["Share your link", "Friends sign up", "You earn rewards"].map((step, i) => (
            <React.Fragment key={i}>
              <span className="text-[9px] font-black uppercase tracking-widest text-black/20 dark:text-white/20" style={{ fontFamily: FONTS.ui }}>{step}</span>
              {i < 2 && <span className="text-black/10 dark:text-white/10">→</span>}
            </React.Fragment>
          ))}
        </div>
        <p className="text-center text-[9px] text-black/15 dark:text-white/15 uppercase tracking-widest">*Terms and conditions apply</p>
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Referrals</p>
      </div>
    </div>
  );
};

export default InviteAndEarn;
