"use client"

import { useState, useEffect } from "react"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import {
  faWallet,
  faIndianRupeeSign,
  faCopy,
  faCheck,
  faInfoCircle,
  faQrcode,
  faCreditCard,
} from "@fortawesome/free-solid-svg-icons"

import { FaCheckCircle, FaExclamationTriangle, FaInfoCircle } from "react-icons/fa"
import { Toast } from "flowbite-react"
import { useNavigate } from "react-router-dom"
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';
import { API_URL } from '@/utils/constants';

function Deposit() {
  const COLORS = useColors();
  const [amount, setAmount] = useState("")
  const [activeTab, setActiveTab] = useState("upi")
  const [copiedUPI, setCopiedUPI] = useState(false)
  const [selectedOption, setSelectedOption] = useState("upi1")
  const [upi, setUpi] = useState("")
  const [upi2, setUpi2] = useState("")
  const [bank, setBank] = useState([])
  const [utr, setUtr] = useState("")
  const [mode, setMode] = useState("")
  const navigate = useNavigate()
  const availableDepositOptions = sessionStorage.getItem("deposit_options") || "100,500,1000,2000,5000"
  const userId = sessionStorage.getItem("account_id")
  const authSecretKey = sessionStorage.getItem("auth_secret_key")
  const [toasts, setToasts] = useState([])

  const paymentOptions = [
    { id: "upi1", name: "UPI 1", type: "upi", logo: faQrcode },
    { id: "upi2", name: "UPI 2", type: "upi", logo: faQrcode },
    { id: "3", name: "BANK", type: "bank", logo: faCreditCard },
    { id: "casypay", name: "CASYPAY", type: "gateway2", logo: faCreditCard },
  ]

  const addToast = (message, type = "info") => {
    const COLORS = useColors();
    const id = Date.now()
    setToasts((prev) => [...prev, { id, message, type }])
    setTimeout(() => {
      setToasts((prev) => prev.filter((toast) => toast.id !== id))
    }, 4000)
  }

  const quickAmounts = ["100", "200", "500", "1000", "5000", "7000", "10000", "20000", "50000"]

  const handleQuickAmount = (value) => setAmount(value.toString())

  const generateQRCodeUrl = (upiId) => {
    if (!upiId) return null
    return `https://api.qrserver.com/v1/create-qr-code/?data=upi://pay?pa=${encodeURIComponent(upiId)}&pn=Merchant&cu=INR&size=150x150`
  }

  const qrCodeUrl = generateQRCodeUrl(selectedOption === "upi1" ? upi : upi2)

  useEffect(() => {
    setActiveTab("upi")
    setSelectedOption("upi1")
  }, [])

  useEffect(() => {
    const fetchDepositAddress = async () => {
      try {
        const response = await fetch(`${API_URL}?USER_ID=${userId}`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Route: "route-deposit-info",
            AuthToken: authSecretKey,
          },
        })
        const result = await response.json()
        setUpi(result.UPI.UPI_ID_1)
        setUpi2(result.UPI.UPI_ID_2)
        setBank(result.BANK_DETAILS)
      } catch (error) {
        console.error("Error fetching Deposit Address", error)
      }
    }
    if (userId && authSecretKey) fetchDepositAddress()
  }, [userId, authSecretKey])

  useEffect(() => {
    if (activeTab === "upi") setMode("UPIPay")
    else if (activeTab === "bank") setMode("BankPay")
    else if (activeTab === "gateway2") setMode("CasyPay")
  }, [activeTab])

  const handleCopyUPI = () => {
    navigator.clipboard.writeText(selectedOption === "upi1" ? upi : upi2)
    setCopiedUPI(true)
    setTimeout(() => setCopiedUPI(false), 2000)
  }

  const handleDeposit = async () => {
    if (!amount || parseFloat(amount) < 100) { addToast("Min. ₹100 required", "error"); return; }
    if (mode === "CasyPay") {
      const paymentURL = "https://pay.winco.cc/gateapi/payments/gateways1/initialisation/casypay.php"
      window.location.href = `${paymentURL}?amount=${amount}&user_id=${userId}`
      return
    }
    if (!utr || utr.trim() === "") { addToast("UTR required", "error"); return; }
    const params = new URLSearchParams({ USER_ID: userId, RECHARGE_AMOUNT: amount, RECHARGE_MODE: mode, RECHARGE_DETAILS: `${utr},${mode}` })
    try {
      const response = await fetch(`${API_URL}?${params.toString()}`, {
        method: "GET",
        headers: { "Content-Type": "application/json", Route: "route-recharge-request", AuthToken: authSecretKey }
      })
      const result = await response.json()
      if (result.status_code === "pending") addToast(`Deposit of ₹${amount} submitted!`, "success")
      else addToast(`Error: ${result.status_code}`, "error")
    } catch (error) { addToast("Error submitting request", "error") }
  }

  return (
    <div className="text-black dark:text-white w-full max-w-md md:max-w-2xl mx-auto overflow-hidden rounded-[2rem] border border-black/10 dark:border-white/10 shadow-3xl relative my-4"
      style={{ backgroundColor: COLORS.bg2, color: COLORS.text }}>

      {/* Subtle Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-32 h-32 bg-brand/30 blur-[60px]"></div>
        <div className="absolute bottom-0 left-0 w-32 h-32 bg-brand/30 blur-[60px]"></div>
      </div>

      {/* Header */}
      <div className="p-5 border-b border-black/5 dark:border-white/5 flex items-center justify-between relative z-10 bg-white/[0.02]">
        <div className="flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl flex items-center justify-center bg-brand shadow-lg">
            <FontAwesomeIcon icon={faWallet} className="text-black dark:text-white text-lg" />
          </div>
          <h2 className="text-xl font-black uppercase tracking-tight" style={{ fontFamily: FONTS.head }}>
            Deposit <span className="text-brand">Funds</span>
          </h2>
        </div>
      </div>

      {/* Toasts */}
      <div className="fixed top-4 right-4 z-[100] space-y-2">
        {toasts.map((toast) => (
          <Toast key={toast.id} onDismiss={() => setToasts((p) => p.filter((t) => t.id !== toast.id))}>
            <div className="flex items-center p-3 rounded-xl bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 text-black dark:text-white shadow-2xl">
              {toast.type === "success" ? <FaCheckCircle className="text-green-500 mr-3" /> : <FaExclamationTriangle className="text-red-500 mr-3" />}
              <span className="text-[11px] font-bold uppercase">{toast.message}</span>
            </div>
          </Toast>
        ))}
      </div>

      {/* Body */}
      <div className="p-5 space-y-6 relative z-10">

        {/* Step 1: Methods */}
        <div>
          <label className="text-[9px] text-black/40 dark:text-white/40 font-black uppercase tracking-widest mb-3 block">1. Payment Method</label>
          <div className="grid grid-cols-4 gap-2">
            {paymentOptions.map((opt) => (
              <button key={opt.id} onClick={() => { setSelectedOption(opt.id); setActiveTab(opt.type); }}
                className={`p-3 rounded-xl border transition-all duration-300 flex flex-col items-center gap-2 relative ${selectedOption === opt.id ? "border-brand bg-brand/10 shadow-md" : "border-black/5 dark:border-white/5 bg-white/[0.03] hover:border-black/20 dark:border-white/20"
                  }`}
                style={{ backgroundColor: selectedOption === opt.id ? "rgba(230,160,0,0.1)" : "rgba(255,255,255,0.03)" }}>
                <div className={`w-8 h-8 rounded-lg flex items-center justify-center ${selectedOption === opt.id ? "bg-brand text-black dark:text-white" : "bg-gray-100 dark:bg-white/5 text-black/30 dark:text-white/30"}`}>
                  <FontAwesomeIcon icon={opt.logo} className="text-sm" />
                </div>
                <span className="text-[8px] font-bold uppercase tracking-tight text-black/60 dark:text-white/60">{opt.name}</span>
                {selectedOption === opt.id && <FontAwesomeIcon icon={faCheck} className="absolute top-2 right-2 text-brand text-[8px]" />}
              </button>
            ))}
          </div>
        </div>

        {/* Step 2: Content */}
        <div className="animate-fadeIn">
          {activeTab === "upi" ? (
            <div className="space-y-4">
              <div className="rounded-2xl p-4 flex flex-col items-center gap-3 border border-black/5 dark:border-white/5" style={{ backgroundColor: "rgba(0,0,0,0.3)" }}>
                <div className="flex justify-between items-center w-full px-1">
                  <span className="text-[8px] text-black/20 dark:text-white/20 font-black uppercase tracking-widest">ID: {selectedOption.toUpperCase()}</span>
                  <button onClick={handleCopyUPI} className="text-brand text-xs active:scale-90 transition-transform"><FontAwesomeIcon icon={copiedUPI ? faCheck : faCopy} /></button>
                </div>
                <p className="text-black dark:text-white font-mono text-sm tracking-wider border-b border-black/10 dark:border-white/10 pb-2 w-full text-center">
                  {selectedOption === "upi1" ? upi || "FETCHING..." : upi2 || "FETCHING..."}
                </p>
                <div className="bg-white p-3 rounded-xl">
                  <img src={qrCodeUrl || "/placeholder.svg"} alt="QR" className="w-28 h-28" />
                </div>
              </div>
            </div>
          ) : activeTab === "bank" ? (
            <div className="rounded-2xl p-4 space-y-3 border border-black/5 dark:border-white/5" style={{ backgroundColor: "rgba(0,0,0,0.3)" }}>
              {[{ l: "Holder", v: bank.ACCOUNT_HOLDER }, { l: "Number", v: bank.ACCOUNT_NUMBER, m: true }, { l: "IFSC", v: bank.IFSC_CODE, m: true }, { l: "Bank", v: bank.BANK_NAME }].map((i, idx) => (
                <div key={idx} className="flex justify-between items-center border-b border-white/[0.05] pb-1.5 last:border-0">
                  <span className="text-[8px] text-black/30 dark:text-white/30 font-black uppercase">{i.l}</span>
                  <span className={`text-[11px] text-black dark:text-white font-bold uppercase ${i.m ? 'font-mono text-brand' : ''}`}>{i.v || "--"}</span>
                </div>
              ))}
            </div>
          ) : (
            <div className="rounded-2xl p-6 text-center border border-black/5 dark:border-white/5" style={{ backgroundColor: "rgba(0,0,0,0.3)" }}>
              <FontAwesomeIcon icon={faCreditCard} className="text-brand text-2xl mb-3" />
              <p className="text-black dark:text-white text-[11px] font-black uppercase">Instant Secure Gateway</p>
            </div>
          )}
        </div>

        {/* Step 3: Input */}
        <div className="space-y-5">
          <div className="text-center">
            <label className="text-[9px] text-black/40 dark:text-white/40 font-black uppercase tracking-widest mb-3 block">2. Enter Amount</label>
            <div className="relative max-w-[180px] mx-auto">
              <div className="absolute inset-y-0 left-4 flex items-center text-brand text-lg"><FontAwesomeIcon icon={faIndianRupeeSign} /></div>
              <input
                type="number"
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                placeholder="0"
                className="w-full py-4 pl-10 pr-4 rounded-2xl border border-black/10 dark:border-white/10 text-2xl font-black text-center focus:outline-none focus:border-brand/40 shadow-inner"
                style={{ backgroundColor: COLORS.bg3, color: COLORS.text, fontFamily: FONTS.head }}
              />
            </div>
          </div>

          <div className="grid grid-cols-3 md:grid-cols-6 gap-2 max-w-[280px] md:max-w-none mx-auto">
            {quickAmounts.map((v) => (
              <button key={v} onClick={() => handleQuickAmount(v)}
                className={`py-2 rounded-lg border text-[9px] font-black transition-all ${amount === v.toString() ? "bg-brand border-brand text-black dark:text-white" : "border-black/5 dark:border-white/5 bg-white/[0.03] text-black/40 dark:text-white/40"}`}
                style={{ backgroundColor: amount === v.toString() ? COLORS.brand : "rgba(255,255,255,0.03)" }}>
                ₹{parseInt(v)}
              </button>
            ))}
          </div>

          {activeTab !== "gateway" && activeTab !== "gateway2" && (
            <div className="max-w-[240px] mx-auto pt-1">
              <label className="text-[8px] text-black/20 dark:text-white/20 font-black uppercase tracking-widest mb-2 block text-center">3. Transaction UTR</label>
              <input
                type="text"
                value={utr}
                onChange={(e) => setUtr(e.target.value)}
                placeholder="12 DIGIT NUMBER"
                className="w-full py-3 px-4 rounded-xl border border-black/10 dark:border-white/10 text-center font-mono text-sm tracking-widest placeholder:text-black/5 dark:text-white/5 focus:outline-none focus:border-brand/30"
                style={{ backgroundColor: COLORS.bg3, color: COLORS.brand }}
              />
            </div>
          )}

          <button onClick={handleDeposit} disabled={!(amount && parseFloat(amount) > 0 && (activeTab.includes('gateway') || utr))}
            className={`w-full py-5 rounded-2xl text-black dark:text-white font-black uppercase tracking-[0.4em] text-[12px] flex items-center justify-center gap-3 transition-all active:scale-95 shadow-lg ${amount && parseFloat(amount) > 0 && (activeTab.includes('gateway') || utr) ? "opacity-100" : "opacity-20 pointer-events-none"
              }`}
            style={{ background: COLORS.brandGradient }}>
            <FontAwesomeIcon icon={faWallet} />
            <span>Continue</span>
          </button>
        </div>
      </div>

      <div className="pb-6 text-center opacity-10">
        <p className="text-[7px] uppercase font-black tracking-[1em]">Authorized Infrastructure</p>
      </div>
    </div>
  )
}

export default Deposit
