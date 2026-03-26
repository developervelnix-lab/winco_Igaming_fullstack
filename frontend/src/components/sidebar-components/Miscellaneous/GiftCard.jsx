import React, { useState, useEffect } from 'react';
import { FaGift, FaCheckCircle, FaExclamationTriangle, FaClock } from 'react-icons/fa';
import { API_URL } from '@/utils/constants';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const GiftCardRedemption = () => {
  const COLORS = useColors();
  const [redeemCode, setRedeemCode] = useState('');
  const [toast, setToast] = useState(null);
  const authSecretKey = sessionStorage.getItem('auth_secret_key');
  const accountId = sessionStorage.getItem('account_id');

  const showToast = (message, type = 'info') => {
    setToast({ message, type });
  };

  useEffect(() => {
    if (toast) {
      const timer = setTimeout(() => setToast(null), 4000);
      return () => clearTimeout(timer);
    }
  }, [toast]);

  const fetchClaimGiftCard = async () => {
    if (!authSecretKey) {
      showToast('Authentication required!', 'error');
      return;
    }

    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'AuthToken': authSecretKey,
          'Route': 'route-claim-giftcard'
        },
        body: JSON.stringify({
          "USER_ID": accountId,
          "GIFTCARD_ID": redeemCode
        })
      });

      const result = await response.json();
      
      if (result.status_code === "success") {
        showToast("Successfully claimed gift card!", 'success');
        setRedeemCode('');
      } else {
        showToast(result.message || 'Invalid gift card code', 'error');
      }
    } catch (error) {
      showToast('Error processing request. Please try again.', 'error');
    }
  }

  const handleSubmitCode = () => {
    if (!redeemCode) {
      showToast('Please enter a redeem code', 'error');
      return;
    }
    fetchClaimGiftCard();
  }

  return (
    <div className="w-[96%] max-w-lg mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
      style={{ backgroundColor: COLORS.bg2 }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-48 h-48 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Custom Toast */}
      {toast && (
        <div className="fixed top-6 left-1/2 -translate-x-1/2 z-[100] animate-fadeIn">
          <div className={`flex items-center p-4 rounded-xl border shadow-2xl backdrop-blur-xl ${
            toast.type === 'success' ? 'bg-black/10 dark:bg-black/90 border-green-500/30 text-black dark:text-white' : 'bg-black/10 dark:bg-black/90 border-red-500/30 text-black dark:text-white'
          }`}>
            {toast.type === 'success' ? <FaCheckCircle className="text-green-500 mr-3 text-xl" /> : <FaExclamationTriangle className="text-red-500 mr-3 text-xl" />}
            <span className="text-xs font-black uppercase tracking-wide">{toast.message}</span>
          </div>
        </div>
      )}

      {/* Header */}
      <div className="p-5 md:p-7 border-b border-black/5 dark:border-white/5 text-center relative z-10 bg-white/[0.02]">
        <div className="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-xl mx-auto mb-4"
          style={{ background: COLORS.brandGradient }}>
          <FaGift />
        </div>
        <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
          Gift Card <span style={{ color: COLORS.brand }}>Redemption</span>
        </h2>
        <p className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30 mt-2">Enter your code below to claim rewards</p>
      </div>

      {/* Form Content */}
      <div className="p-5 md:p-6 space-y-5 relative z-10">
        <div className="space-y-2">
          <label className="block text-[9px] font-black uppercase tracking-[0.2em] text-black/40 dark:text-white/40 ml-1" style={{ fontFamily: FONTS.ui }}>
            Redeem Code
          </label>
          <input
            type="text"
            value={redeemCode}
            onChange={(e) => setRedeemCode(e.target.value)}
            className="w-full px-4 py-3 rounded-lg bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-[13px] font-black tracking-widest uppercase transition-all duration-300 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand shadow-inner placeholder-black/30 dark:placeholder-white/30 text-black dark:text-white"
            placeholder="XXXX-XXXX-XXXX"
            style={{ 
              fontFamily: FONTS.ui 
            }}
          />
        </div>

        <button 
          onClick={handleSubmitCode}
          className="w-full py-3 rounded-lg font-black uppercase tracking-widest text-[11px] text-black dark:text-white shadow-lg active:scale-95 transition-all duration-300 relative overflow-hidden group"
          style={{ background: COLORS.brandGradient, fontFamily: FONTS.ui }}
        >
          <div className="absolute inset-0 bg-gray-100 dark:bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <span className="relative">Claim Rewards Now</span>
        </button>

        <div className="flex items-center justify-center gap-2 pt-4 opacity-30 select-none">
          <FaClock className="text-xs" />
          <span className="text-[9px] font-black uppercase tracking-widest">Rewards added instantly</span>
        </div>
      </div>

      {/* Footer Decoration */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Rewards System</p>
      </div>
    </div>
  );
};

export default GiftCardRedemption;