"use client"

import { useState, useEffect } from "react"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import {
  faWallet,
  faIndianRupeeSign,
  faCheck,
  faInfoCircle,
  faCreditCard,
  faPlus,
  faChevronDown,
  faSearch,
  faTimes,
  faUniversity
} from "@fortawesome/free-solid-svg-icons"
import { Toast } from "flowbite-react"
import { FaCheckCircle, FaExclamationTriangle, FaInfoCircle } from "react-icons/fa"
import { useColors } from '../../hooks/useColors';
import { FONTS } from '../../constants/theme';// Since I cannot import API_URL directly from @/utils/constants in this environment 
// (it might fail due to alias resolution), I will use the one from sessionStorage or fallback
const STATIC_API_URL = "https://pay.winco.cc/gateapi/v3/route.php" 

const Withdraw = () => {
  const COLORS = useColors();
  const [amount, setAmount] = useState("")
  const [showAddBankPopup, setShowAddBankPopup] = useState(false)
  const [addedBankAccounts, setAddedBankAccounts] = useState([])
  const [selectedAccount, setSelectedAccount] = useState("")
  const [showBankDropdown, setShowBankDropdown] = useState(false)
  const [accountBalance, setAccountBalance] = useState("0")
  const authSecretKey = sessionStorage.getItem("auth_secret_key")
  const userId = sessionStorage.getItem("account_id")
  const availableBalance = sessionStorage.getItem("avl_balance")
  const [toasts, setToasts] = useState([])
  const [availableBanks, setAvailableBanks] = useState([])
  const [bankSearch, setBankSearch] = useState("")

  const [formData, setFormData] = useState({
    realName: "",
    accountNumber: "",
    selectedBank: "",
    ifscCode: "",
  })

  const addToast = (message, type = "info") => {
    const id = Date.now()
    setToasts((prev) => [...prev, { id, message, type }])
    setTimeout(() => {
      setToasts((prev) => prev.filter((toast) => toast.id !== id))
    }, 4000)
  }

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const fetchBankCards = async (userId) => {
    try {
      const response = await fetch(`${STATIC_API_URL}?USER_ID=${userId}&PAGE_NUM=1`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Route: "route-get-bankcards",
          AuthToken: authSecretKey,
        },
      })
      const result = await response.json()
      if (result.status_code === "success") {
        setAddedBankAccounts(result.data)
        const primaryBank = result.data.find((bank) => bank.c_is_primary === "true")
        if (primaryBank) setSelectedAccount(primaryBank.c_bank_id)
      }
    } catch (error) {
      console.error("Error fetching bank cards", error)
    }
  }

  const fetchBankList = async () => {
    try {
      const response = await fetch(STATIC_API_URL, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Route: "route-get-banklist",
          AuthToken: authSecretKey,
        },
      })
      const result = await response.json()
      setAvailableBanks(result.data.banklist)
    } catch (error) {
      console.error("Error fetching bank list", error)
    }
  }

  const addBankDetails = async () => {
    const { realName, accountNumber, selectedBank, ifscCode } = formData
    if (!realName || !accountNumber || !selectedBank || !ifscCode) {
      addToast("Please fill all fields", "error")
      return
    }

    const params = new URLSearchParams({
      USER_ID: userId,
      BENEFICIARY_NAME: realName,
      USER_BANK_NAME: selectedBank,
      USER_BANK_ACCOUNT: accountNumber,
      USER_BANK_IFSC_CODE: ifscCode,
      IS_PRIMARY: "true",
      CARD_METHOD: "bank",
    })

    try {
      const response = await fetch(`${STATIC_API_URL}?${params.toString()}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          AuthToken: authSecretKey,
          Route: "route-add-bankcard",
        },
      })
      const result = await response.json()
      if (result.status_code === "success") {
        addToast("Bank added successfully", "success")
        fetchBankCards(userId)
        setShowAddBankPopup(false)
        setFormData({ realName: "", accountNumber: "", selectedBank: "", ifscCode: "" })
      } else {
        addToast(`Error: ${result.status_code}`, "error")
      }
    } catch (error) {
      addToast("Failed to add bank account", "error")
    }
  }

  const setPrimaryBankCard = async (cardId) => {
    try {
      const response = await fetch(`${STATIC_API_URL}?USER_ID=${userId}&CARD_ID=${cardId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Route: "route-set-bankcard-primary",
          AuthToken: authSecretKey,
        },
      })
      const result = await response.json()
      if (result.status_code === "success") {
        fetchBankCards(userId)
        setSelectedAccount(cardId)
      }
    } catch (error) {
      console.error("Error setting primary bank", error)
    }
  }

  useEffect(() => {
    fetchBankCards(userId)
    setAccountBalance(availableBalance || "0")
    fetchBankList()
  }, [authSecretKey])

  const handleWithdrawal = async () => {
    if (!selectedAccount) { addToast("Select a bank account", "error"); return; }
    if (!amount || parseFloat(amount) < 100 || parseFloat(amount) > 50000) {
      addToast("Amount: ₹100 - ₹50,000", "error"); return;
    }

    try {
      const response = await fetch(STATIC_API_URL, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Route: "route-withdraw-request",
          AuthToken: authSecretKey,
        },
        body: JSON.stringify({ USER_ID: userId, WITHDRAW_AMOUNT: amount }),
      })
      const result = await response.json()
      if (result.status_code === "success") {
        addToast(`Withdrawal of ₹${amount} initiated!`, "success")
        setAmount("")
      } else {
        addToast(`Failed: ${result.message || result.status_code}`, "error")
      }
    } catch (error) {
      addToast("Error processing request", "error")
    }
  }

  return (
    <div className="text-black dark:text-white w-full max-w-md md:max-w-2xl mx-auto overflow-hidden rounded-[2.5rem] border border-black/10 dark:border-white/10 shadow-3xl relative my-6"
      style={{ backgroundColor: COLORS.bg2, color: COLORS.text }}>
      
      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-32 h-32 bg-brand/30 blur-[60px]"></div>
        <div className="absolute bottom-0 left-0 w-32 h-32 bg-brand/30 blur-[60px]"></div>
      </div>

      {/* Header */}
      <div className="p-6 border-b border-black/5 dark:border-white/5 flex items-center justify-between relative z-10 bg-white/[0.02]">
        <div className="flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl flex items-center justify-center bg-brand shadow-lg">
            <FontAwesomeIcon icon={faWallet} className="text-black dark:text-white text-xl" />
          </div>
          <div>
            <h2 className="text-2xl font-black uppercase tracking-tight" style={{ fontFamily: FONTS.head }}>
              Withdraw <span className="text-brand">Funds</span>
            </h2>
            <div className="flex items-center gap-2 mt-1 opacity-60">
              <div className="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,1)] animate-pulse"></div>
              <span className="text-[10px] font-bold uppercase tracking-widest">Secure Transfer Active</span>
            </div>
          </div>
        </div>
      </div>

      {/* Toasts */}
      <div className="fixed top-6 right-6 z-[100] space-y-3">
        {toasts.map((toast) => (
          <Toast key={toast.id} onDismiss={() => setToasts((p) => p.filter((t) => t.id !== toast.id))}>
            <div className="flex items-center p-3 rounded-xl bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 text-black dark:text-white shadow-2xl backdrop-blur-xl">
              {toast.type === "success" ? <FaCheckCircle className="text-green-500 mr-3" /> : <FaExclamationTriangle className="text-red-500 mr-3" />}
              <span className="text-[12px] font-bold uppercase">{toast.message}</span>
            </div>
          </Toast>
        ))}
      </div>

      {/* Body */}
      <div className="p-6 space-y-8 relative z-10">
        
        {/* Balance Display */}
        <div className="bg-white/[0.03] border border-black/5 dark:border-white/5 rounded-3xl p-6 flex justify-between items-center group">
          <div>
            <p className="text-[10px] text-black/30 dark:text-white/30 font-black uppercase tracking-widest mb-1">Available Balance</p>
            <p className="text-3xl font-black text-brand" style={{ fontFamily: FONTS.head }}>₹{accountBalance}</p>
          </div>
          <div className="w-12 h-12 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-brand/10 transition-colors">
             <FontAwesomeIcon icon={faIndianRupeeSign} className="text-black/20 dark:text-white/20 group-hover:text-brand transition-colors" />
          </div>
        </div>

        {/* Bank Selection */}
        <div className="space-y-4">
          <label className="text-[10px] text-black/40 dark:text-white/40 font-black uppercase tracking-widest block px-1">1. Destination Account</label>
          
          {addedBankAccounts.length > 0 ? (
            <div className="space-y-3">
               <div className="relative">
                 <select
                    value={selectedAccount}
                    onChange={(e) => setPrimaryBankCard(e.target.value)}
                    className="w-full bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-2xl py-4 px-5 text-sm font-bold appearance-none focus:outline-none focus:border-brand/40 transition-colors"
                    style={{ backgroundColor: COLORS.bg3, color: COLORS.text }}
                  >
                    {addedBankAccounts.map((account) => (
                      <option key={account.c_bank_id} value={account.c_bank_id}>
                        {account.c_is_primary === "true" ? "⭐ " : ""}{account.c_bank_name} (****{account.c_bank_account.slice(-4)})
                      </option>
                    ))}
                  </select>
                  <FontAwesomeIcon icon={faChevronDown} className="absolute right-5 top-1/2 -translate-y-1/2 text-black/20 dark:text-white/20 pointer-events-none" />
               </div>
               <button onClick={() => setShowAddBankPopup(true)} className="w-full py-3 border border-dashed border-black/10 dark:border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-black/30 dark:text-white/30 hover:text-brand hover:border-brand/30 transition-all">
                  + Add New Bank Account
               </button>
            </div>
          ) : (
            <button onClick={() => setShowAddBankPopup(true)} className="w-full py-10 border-2 border-dashed border-black/5 dark:border-white/5 rounded-3xl flex flex-col items-center gap-3 group hover:border-brand/20 transition-all">
               <div className="w-12 h-12 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center group-hover:bg-brand/10 transition-all text-black/20 dark:text-white/20 group-hover:text-brand">
                  <FontAwesomeIcon icon={faPlus} />
               </div>
               <p className="text-[11px] font-black uppercase tracking-widest opacity-30 group-hover:opacity-100 transition-opacity">Connect Bank Account</p>
            </button>
          )}
        </div>

        {/* Amount Input */}
        <div className="space-y-5">
           <div className="text-center">
              <label className="text-[10px] text-black/40 dark:text-white/40 font-black uppercase tracking-widest mb-4 block">2. Withdrawal Amount</label>
              <div className="relative max-w-[200px] mx-auto">
                <div className="absolute inset-y-0 left-5 flex items-center text-brand text-2xl"><FontAwesomeIcon icon={faIndianRupeeSign} /></div>
                <input 
                  type="number" 
                  value={amount} 
                  onChange={(e) => setAmount(e.target.value)} 
                  placeholder="0"
                  className="w-full py-5 pl-12 pr-4 rounded-3xl border border-black/10 dark:border-white/10 text-4xl font-black text-center focus:outline-none focus:border-brand/40 shadow-inner"
                  style={{ backgroundColor: COLORS.bg3, color: COLORS.text, fontFamily: FONTS.head }} 
                />
              </div>
           </div>

           <div className="grid grid-cols-4 md:grid-cols-8 gap-2">
              {["500", "1000", "5000", "10000"].map((v) => (
                <button key={v} onClick={() => setAmount(v)}
                  className={`py-3 rounded-xl border text-[10px] font-black transition-all ${amount === v ? "bg-brand border-brand text-black dark:text-white shadow-lg" : "border-black/5 dark:border-white/5 bg-white/[0.03] text-black/40 dark:text-white/40"}`}>
                  ₹{parseInt(v)}
                </button>
              ))}
           </div>

           <button onClick={handleWithdrawal} disabled={!amount || !selectedAccount}
            className={`w-full py-6 rounded-3xl text-black dark:text-white font-black uppercase tracking-[0.5em] text-xs flex items-center justify-center gap-4 transition-all active:scale-95 shadow-xl ${
              amount && selectedAccount ? "opacity-100 hover:shadow-brand/20" : "opacity-20 pointer-events-none"
            }`} 
            style={{ background: COLORS.brandGradient }}>
              <FontAwesomeIcon icon={faWallet} />
              <span>Confirm Payout</span>
           </button>
        </div>

        <div className="flex items-start gap-4 p-4 bg-brand/5 rounded-2xl border border-brand/10">
           <FontAwesomeIcon icon={faInfoCircle} className="text-brand text-sm mt-0.5" />
           <p className="text-[9px] text-black/40 dark:text-white/40 uppercase font-black leading-relaxed tracking-wider">
              Payouts are processed instantly. Large amounts may take up to 24 hours for security verification.
           </p>
        </div>
      </div>

      <div className="pb-8 text-center opacity-10">
        <p className="text-[8px] uppercase font-black tracking-[1em]">Authorized Payout Node</p>
      </div>

      {/* Add Bank Popup */}
      {showAddBankPopup && (
        <div className="fixed inset-0 bg-black/10 dark:bg-black/90 backdrop-blur-md flex items-center justify-center p-4 z-[100] animate-fadeIn">
          <div className="bg-gray-50 dark:bg-[#111111] border border-black/10 dark:border-white/10 rounded-[2.5rem] p-8 w-full max-w-sm relative shadow-[0_0_100px_rgba(0,0,0,1)]">
            <button onClick={() => setShowAddBankPopup(false)} className="absolute top-6 right-6 text-black/20 dark:text-white/20 hover:text-black dark:text-white transition-colors">
               <FontAwesomeIcon icon={faTimes} />
            </button>
            <h3 className="text-xl font-black text-black dark:text-white uppercase tracking-tighter mb-8" style={{ fontFamily: FONTS.head }}>Add Bank <span className="text-brand">Account</span></h3>

            <div className="space-y-5">
              <div className="space-y-2">
                <label className="text-[9px] text-brand font-black uppercase tracking-widest ml-1">Account Holder Name</label>
                <input name="realName" value={formData.realName} onChange={handleInputChange} placeholder="AS PER BANK RECORDS" 
                  className="w-full bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-xl py-4 px-5 text-xs font-bold focus:outline-none focus:border-brand/40"
                  style={{ backgroundColor: COLORS.bg3, color: COLORS.text }} />
              </div>

              <div className="space-y-2">
                <label className="text-[9px] text-brand font-black uppercase tracking-widest ml-1">Account Number</label>
                <input name="accountNumber" value={formData.accountNumber} onChange={handleInputChange} placeholder="DIGITS ONLY" 
                  className="w-full bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-xl py-4 px-5 text-xs font-bold focus:outline-none focus:border-brand/40"
                  style={{ backgroundColor: COLORS.bg3, color: COLORS.text }} />
              </div>

              <div className="space-y-2 relative">
                <label className="text-[9px] text-brand font-black uppercase tracking-widest ml-1">Select Bank</label>
                <button onClick={() => setShowBankDropdown(!showBankDropdown)} className="w-full bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-xl py-4 px-5 text-xs font-bold text-left flex justify-between items-center bg-gray-100 dark:bg-black">
                  <span className={formData.selectedBank ? "text-black dark:text-white" : "text-black/20 dark:text-white/20"}>{formData.selectedBank || "CHOOSE BANK"}</span>
                  <FontAwesomeIcon icon={faChevronDown} className="opacity-30" />
                </button>
                {showBankDropdown && (
                  <div className="absolute z-10 w-full mt-2 bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-2xl overflow-hidden shadow-3xl animate-fadeInUp">
                    <div className="p-3 bg-white/[0.03] border-b border-black/5 dark:border-white/5 flex items-center gap-3">
                       <FontAwesomeIcon icon={faSearch} className="text-black/20 dark:text-white/20 text-xs" />
                       <input value={bankSearch} onChange={(e) => setBankSearch(e.target.value)} placeholder="SEARCH..." 
                         className="bg-transparent border-none text-[10px] font-black w-full focus:ring-0 text-black dark:text-white placeholder:text-black/10 dark:text-white/10" autoFocus />
                    </div>
                    <div className="max-h-40 overflow-y-auto scrollbar-hide">
                      {availableBanks
                        .filter(b => b.bankName.toLowerCase().includes(bankSearch.toLowerCase()))
                        .map(b => (
                          <button key={b.bankName} onClick={() => { setFormData(p => ({ ...p, selectedBank: b.bankName })); setShowBankDropdown(false); }}
                            className="w-full text-left px-5 py-3 text-[10px] font-bold text-black/50 dark:text-white/50 hover:bg-brand hover:text-black dark:text-white transition-colors">
                            {b.bankName}
                          </button>
                        ))}
                    </div>
                  </div>
                )}
              </div>

              <div className="space-y-2">
                <label className="text-[9px] text-brand font-black uppercase tracking-widest ml-1">IFSC Code</label>
                <input name="ifscCode" value={formData.ifscCode} onChange={handleInputChange} placeholder="SBIN000XXXX" 
                  className="w-full bg-gray-100 dark:bg-black border border-black/10 dark:border-white/10 rounded-xl py-4 px-5 text-xs font-bold focus:outline-none focus:border-brand/40"
                  style={{ backgroundColor: COLORS.bg3, color: COLORS.text }} />
              </div>

              <button onClick={addBankDetails} className="w-full py-5 rounded-2xl text-black dark:text-white font-black uppercase tracking-[0.4em] text-[10px] shadow-lg mt-4 active:scale-95 transition-all"
                style={{ background: COLORS.brandGradient }}>
                Add Account
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default Withdraw
