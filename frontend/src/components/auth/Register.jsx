import React, { useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTimes } from "@fortawesome/free-solid-svg-icons";
import generateRandomToken from "@/utils/randomTokenGenerator";
import { Toast } from "flowbite-react";
import { HiCheck, HiX } from "react-icons/hi";
import { API_URL } from "@/utils/constants";
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';

const Register = ({ onSwitchToLogin, onClose }) => {
  const COLORS = useColors();
  const [phoneNumber, setPhoneNumber] = useState("");
  const [otp, setOtp] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [referralCode, setReferralCode] = useState("");
  const [otpSent, setOtpSent] = useState(false);
  const [otpError, setOtpError] = useState("");
  const [passwordError, setPasswordError] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [toast, setToast] = useState(null);

  const showToast = (type, message) => {
    setToast({ type, message });
    setTimeout(() => setToast(null), 3000);
  };

  const isValidPhoneNumber = (number) => /^\d{10}$/.test(number);

  const handlePhoneChange = (e) => {
    setPhoneNumber(e.target.value);
  };

  const handleOtpChange = (e) => {
    const value = e.target.value;
    if (value.length <= 6 && /^\d*$/.test(value)) {
      setOtp(value);
      setOtpError("");
    }
  };

  const validatePasswords = (pass, confirmPass) => {
    if (!pass || !confirmPass) return "";
    return pass !== confirmPass ? "Passwords do not match" : "";
  };

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
    setPasswordError(validatePasswords(e.target.value, confirmPassword));
  };

  const handleConfirmPasswordChange = (e) => {
    setConfirmPassword(e.target.value);
    setPasswordError(validatePasswords(password, e.target.value));
  };

  const handleReferralChange = (e) => {
    setReferralCode(e.target.value);
  };

  const handleGetOtp = async () => {
    if (!isValidPhoneNumber(phoneNumber)) {
      setOtpError("Please enter a valid 10-digit phone number");
      return;
    }
    setIsLoading(true);
    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Route: "route-send-sms",
          AuthToken: generateRandomToken(32),
        },
        body: JSON.stringify({ SMS_MOBILE: phoneNumber }),
      });

      const data = await response.json();

      if (data.status_code === "otp_error") {
        setOtpError("We failed to send OTP! Please try again!");
        showToast("error", "Failed to send OTP. Please try again!");
      } else if (data.status_code === "otp_limit_error") {
        setOtpError("You have reached the limit of OTP requests! Please try again later!");
        showToast("error", "OTP request limit reached. Please try later!");
      } else if (data.status_code === "success") {
        setOtpSent(true);
        setIsLoading(false);
        setOtpError("");
        showToast("success", "OTP sent successfully!");
      }
    } catch (error) {
      setOtpError("Something went wrong! Please try again.");
      showToast("error", "Something went wrong. Please try again!");
    } finally {
      setIsLoading(false);
    }
  };

  const handleRegister = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    if (!otp || otp.length !== 6) {
      setOtpError("Please enter a valid 6-digit OTP");
      return;
    }
    if (password !== confirmPassword) {
      setPasswordError("Passwords do not match");
      return;
    }

    try {
      const response = await fetch(API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Route: "route-create-account",
          AuthToken: generateRandomToken(32),
        },
        body: JSON.stringify({
          SIGNUP_MOBILE: phoneNumber,
          SIGNUP_PASSWORD: password,
          SIGNUP_OTP: otp,
          SIGNUP_INVITE_CODE: referralCode || "",
        }),
      });

      const data = await response.json();

      if (data.status_code === "success") {
        showToast("success", "Registration successful!");
        sessionStorage.setItem("auth_secret_key", data.data[0].auth_secret_key);
        sessionStorage.setItem("account_id", data.data[0].account_id);
        if (onClose) onClose();
      } else if (data.status_code === "already_registered") {
        showToast("error", "Already registered! Please login.");
      } else {
        showToast("error", "Registration failed! Please try again.");
      }
    } catch (error) {
      showToast("error", "Something went wrong! Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <div className="mx-auto w-full max-w-md my-4 px-4">
        <div 
          className="relative p-6 rounded-2xl shadow-2xl w-full border border-black/10 dark:border-white/10 overflow-hidden transition-all duration-300 mx-auto"
          style={{ backgroundColor: COLORS.bg2, backdropFilter: 'blur(10px)' }}
        >
          {/* Close Button */}
          <button
            onClick={onClose}
            className="absolute top-4 right-4 text-black/60 dark:text-white/60 hover:text-black dark:text-white bg-black/5 dark:bg-white/5 hover:bg-gray-100 dark:bg-white/10 rounded-full p-2 transition-all"
          >
            <FontAwesomeIcon icon={faTimes} className="w-4 h-4" />
          </button>

          {/* Logo/Title Area */}
          <div className="text-center mb-8">
            <h2 className="text-3xl font-black uppercase tracking-wider mb-2" style={{ color: COLORS.text, fontFamily: FONTS.display }}>
              Create <span style={{ color: COLORS.brand }}>Account</span>
            </h2>
            <p className="text-sm" style={{ color: COLORS.muted, fontFamily: FONTS.ui }}>
              Join us and start your winning journey!
            </p>
          </div>

          <form onSubmit={handleRegister} className="space-y-4 relative z-10">
            <div className="space-y-2">
              <label className="text-xs font-bold text-black/50 dark:text-white/50 uppercase tracking-widest block px-1" style={{ fontFamily: FONTS.head }}>
                Mobile Number
              </label>
              <div className="flex gap-2">
                <div className="flex-grow relative group">
                  <div className="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <span className="text-sm font-bold" style={{ color: COLORS.brand }}>+91</span>
                  </div>
                  <input
                    type="tel"
                    value={phoneNumber}
                    onChange={handlePhoneChange}
                    className="w-full pr-4 py-3 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl text-black dark:text-white placeholder-black/30 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all text-sm shadow-inner"
                    placeholder="9999999999"
                    disabled={otpSent}
                    required
                    style={{ fontFamily: FONTS.ui, paddingLeft: '70px' }}
                  />
                </div>
                <button
                  type="button"
                  onClick={handleGetOtp}
                  disabled={isLoading || otpSent}
                  className={`px-4 py-3 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 shadow-lg whitespace-nowrap text-xs ${
                    otpSent ? "bg-black/5 dark:bg-white/10 text-black/40 dark:text-white/40" : "text-white"
                  }`}
                  style={{ 
                    background: otpSent ? 'transparent' : COLORS.brandGradient,
                    fontFamily: FONTS.head 
                  }}
                >
                  {isLoading ? "..." : otpSent ? "SENT" : "Get OTP"}
                </button>
              </div>
              {otpError && (
                <div className="bg-red-500/10 border border-red-500/20 py-2 px-4 rounded-lg">
                  <p className="text-red-500 text-xs text-center font-medium">{otpError}</p>
                </div>
              )}
            </div>

            {otpSent && (
              <div className="space-y-4 animate-fade-in">
                <div className="space-y-2">
                  <label className="text-xs font-bold text-black/50 dark:text-white/50 uppercase tracking-widest block px-1" style={{ fontFamily: FONTS.head }}>
                    6-Digit OTP
                  </label>
                  <input
                    type="text"
                    value={otp}
                    onChange={handleOtpChange}
                    className="w-full px-4 py-3 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl text-black dark:text-white placeholder-black/30 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all text-sm tracking-[0.5em] text-center font-bold shadow-inner"
                    placeholder="••••••"
                    maxLength={6}
                    required
                    style={{ fontFamily: FONTS.ui }}
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <label className="text-xs font-bold text-black/50 dark:text-white/50 uppercase tracking-widest block px-1" style={{ fontFamily: FONTS.head }}>
                      Password
                    </label>
                    <input
                      type="password"
                      value={password}
                      onChange={handlePasswordChange}
                      className="w-full px-4 py-3 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl text-black dark:text-white placeholder-black/30 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all text-sm shadow-inner"
                      placeholder="Create password"
                      required
                      style={{ fontFamily: FONTS.ui }}
                    />
                  </div>
                  <div className="space-y-2">
                    <label className="text-xs font-bold text-black/50 dark:text-white/50 uppercase tracking-widest block px-1" style={{ fontFamily: FONTS.head }}>
                      Confirm
                    </label>
                    <input
                      type="password"
                      value={confirmPassword}
                      onChange={handleConfirmPasswordChange}
                      className="w-full px-4 py-3 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl text-black dark:text-white placeholder-black/30 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all text-sm shadow-inner"
                      placeholder="Confirm password"
                      required
                      style={{ fontFamily: FONTS.ui }}
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="text-xs font-bold text-black/50 dark:text-white/50 uppercase tracking-widest block px-1" style={{ fontFamily: FONTS.head }}>
                    Referral Code <span className="opacity-40 italic">(Optional)</span>
                  </label>
                  <input
                    type="text"
                    value={referralCode}
                    onChange={handleReferralChange}
                    className="w-full px-4 py-3 bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 rounded-xl text-black dark:text-white placeholder-black/30 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand transition-all text-sm shadow-inner"
                    placeholder="Enter bonus code"
                    style={{ fontFamily: FONTS.ui }}
                  />
                </div>

                {passwordError && (
                  <div className="bg-red-500/10 border border-red-500/20 py-2 px-4 rounded-lg">
                    <p className="text-red-500 text-xs text-center font-medium">{passwordError}</p>
                  </div>
                )}

                <button
                  type="submit"
                  disabled={isLoading || otp.length !== 6 || !password || !confirmPassword || passwordError}
                  className="w-full py-4 mt-2 text-white font-black uppercase tracking-[0.2em] rounded-xl transition-all duration-300 text-sm hover:scale-[1.02] active:scale-[0.98] shadow-xl disabled:opacity-50 disabled:hover:scale-100 disabled:cursor-not-allowed group"
                  style={{ background: COLORS.brandGradient, fontFamily: FONTS.head }}
                >
                  <span className="flex items-center justify-center gap-2">
                    {isLoading ? "Processing..." : "REGISTER NOW"}
                    {!isLoading && (
                      <svg className="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                      </svg>
                    )}
                  </span>
                </button>
              </div>
            )}
          </form>

          <div className="mt-8 text-center border-t border-black/5 dark:border-white/5 pt-6">
            <p className="text-xs" style={{ color: COLORS.muted, fontFamily: FONTS.ui }}>
              Already registered?{" "}
              <button 
                onClick={onSwitchToLogin} 
                className="font-bold hover:underline transition-all"
                style={{ color: COLORS.brand }}
              >
                LOGIN HERE
              </button>
            </p>
          </div>
        </div>
      </div>

      {/* Toast Container */}
      {toast && (
        <div
          className="fixed top-4 right-4 sm:w-auto w-full flex sm:justify-end justify-center z-50"
        >
          <Toast>
            {/* Icon */}
            <div
              className={`inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg ${
                toast.type === "success"
                  ? "bg-green-100 text-green-700"
                  : "bg-red-100 text-red-700"
              }`}
            >
              {toast.type === "success" ? (
                <HiCheck className="h-5 w-5" />
              ) : (
                <HiX className="h-5 w-5" />
              )}
            </div>

            {/* Message */}
            <div className="ml-3 text-sm font-normal">{toast.message}</div>

            {/* Dismiss Button */}
            <Toast.Toggle onDismiss={() => setToast(null)} />
          </Toast>
        </div>
      )}

    </>
  );
};

export default Register;