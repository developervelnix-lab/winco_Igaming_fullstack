import React, { useState } from "react";
import { Eye, EyeOff, Lock, X } from "lucide-react";
import { FaKey, FaCheckCircle, FaExclamationTriangle } from "react-icons/fa";
import { API_URL } from "@/utils/constants";
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const PasswordChangeForm = () => {
  const COLORS = useColors();
  const authSecretKey = sessionStorage.getItem('auth_secret_key');
  const userId = sessionStorage.getItem('account_id');

  const [formData, setFormData] = useState({
    oldPassword: "",
    newPassword: "",
    confirmPassword: "",
  });
  const [showPassword, setShowPassword] = useState({
    oldPassword: false,
    newPassword: false,
    confirmPassword: false,
  });
  const [toast, setToast] = useState({ message: "", type: "", visible: false });

  const togglePasswordVisibility = (field) => {
    setShowPassword((prev) => ({ ...prev, [field]: !prev[field] }));
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const changePassword = async () => {
    if (!authSecretKey) {
      setToast({ message: "Authentication failed. Please log in.", type: "error", visible: true });
      setTimeout(() => setToast(prev => ({ ...prev, visible: false })), 3000);
      return;
    }

    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'AuthToken': authSecretKey,
          'Route': 'route-change-password'
        },
        body: JSON.stringify({
          "USER_ID": userId,
          "OLD_PASSWORD": formData.oldPassword,
          "NEW_PASSWORD": formData.newPassword
        })
      });

      const result = await response.json();

      if (result.status_code == "success") {
        setToast({ message: "Password changed successfully!", type: "success", visible: true });
        setFormData({ oldPassword: "", newPassword: "", confirmPassword: "" });
      } else if (result.status_code == "authorization_error") {
        setToast({ message: "Please login again.", type: "error", visible: true });
      } else if (result.status_code == "password_error" || result.status_code == "old_password_not_match") {
        setToast({ message: "Incorrect old password. Please try again.", type: "error", visible: true });
      } else {
        setToast({ message: "Failed to change password. Please try again.", type: "error", visible: true });
      }
      setTimeout(() => setToast(prev => ({ ...prev, visible: false })), 3000);
    } catch (error) {
      setToast({ message: "Network error. Please check your connection.", type: "error", visible: true });
      setTimeout(() => setToast(prev => ({ ...prev, visible: false })), 3000);
    }
  };

  const handleSubmit = () => {
    if (formData.newPassword !== formData.confirmPassword) {
      setToast({ message: "Passwords do not match.", type: "error", visible: true });
      setTimeout(() => setToast(prev => ({ ...prev, visible: false })), 3000);
      return;
    }
    changePassword();
  };

  return (
    <div className="w-[96%] max-w-lg mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative"
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
          <FaKey />
        </div>
        <div>
          <h2 className="text-xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Change <span style={{ color: COLORS.brand }}>Password</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Account Security</span>
        </div>
      </div>

      {/* Toast */}
      {toast.visible && (
        <div className="fixed top-6 left-1/2 -translate-x-1/2 z-[100] animate-fadeIn">
          <div className={`flex items-center p-4 rounded-xl border shadow-2xl backdrop-blur-xl ${toast.type === 'success' ? 'bg-black/10 dark:bg-black/90 border-green-500/30 text-black dark:text-white' : 'bg-black/10 dark:bg-black/90 border-red-500/30 text-black dark:text-white'}`}>
            {toast.type === 'success' ? <FaCheckCircle className="text-green-500 mr-3 text-xl" /> : <FaExclamationTriangle className="text-red-500 mr-3 text-xl" />}
            <span className="text-xs font-black uppercase tracking-wide">{toast.message}</span>
            <button onClick={() => setToast({ ...toast, visible: false })} className="ml-6 text-xl opacity-30 hover:opacity-100">&times;</button>
          </div>
        </div>
      )}

      {/* Form */}
      <div className="p-6 md:p-8 space-y-5 relative z-10">
        {[
          { label: "Old Password", name: "oldPassword" },
          { label: "New Password", name: "newPassword" },
          { label: "Confirm Password", name: "confirmPassword" },
        ].map(({ label, name }) => (
          <div key={name}>
            <label className="block text-[10px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-2" style={{ fontFamily: FONTS.ui }}>
              {label}
            </label>
            <div className="relative">
              <span className="absolute inset-y-0 left-4 flex items-center" style={{ color: COLORS.brand }}>
                <Lock size={16} />
              </span>
              <input
                type={showPassword[name] ? "text" : "password"}
                name={name}
                value={formData[name]}
                onChange={handleChange}
                className="w-full !pl-11 pr-10 py-2.5 rounded-lg bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-[13px] focus:outline-none focus:border-brand/50 focus:ring-1 focus:ring-brand/50 shadow-inner placeholder-black/30 dark:placeholder-white/30 transition-all text-black dark:text-white"
                placeholder={label}
                style={{ 
                  fontFamily: FONTS.ui,
                  paddingLeft: '44px'
                }}
              />
              <button
                type="button"
                className="absolute inset-y-0 right-4 flex items-center text-black/30 dark:text-white/30 hover:text-black/60 dark:text-white/60 transition-colors"
                onClick={() => togglePasswordVisibility(name)}
              >
                {showPassword[name] ? <EyeOff size={16} /> : <Eye size={16} />}
              </button>
            </div>
          </div>
        ))}

        {/* Buttons */}
        <div className="flex gap-3 pt-3">
          <button
            className="flex-1 px-4 py-2.5 rounded-lg font-black uppercase tracking-widest text-[10px] bg-gray-100 dark:bg-white/5 text-black/50 dark:text-white/50 hover:text-black dark:text-white hover:bg-gray-100 dark:bg-white/10 transition-all duration-300 border border-black/5 dark:border-white/5"
            onClick={() => setFormData({ oldPassword: "", newPassword: "", confirmPassword: "" })}
            style={{ fontFamily: FONTS.ui }}
          >
            Cancel
          </button>
          <button
            className="flex-1 px-4 py-2.5 rounded-lg font-black uppercase tracking-widest text-[10px] text-black dark:text-white shadow-lg active:scale-95 transition-all duration-300 relative overflow-hidden group"
            onClick={handleSubmit}
            style={{ background: COLORS.brandGradient, fontFamily: FONTS.ui }}
          >
            <div className="absolute inset-0 bg-gray-100 dark:bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <span className="relative">Save Password</span>
          </button>
        </div>
      </div>

      {/* Footer */}
      <div className="pb-6 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Security</p>
      </div>
    </div>
  );
};

export default PasswordChangeForm;
