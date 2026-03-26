"use client"

import React, { useEffect, useState } from "react"
import { FaClipboard, FaWallet, FaExchangeAlt, FaHistory, FaSearch, FaCheckCircle, FaExclamationTriangle, FaClock, FaInfoCircle } from "react-icons/fa"
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';// Re-defining API_URL for consistency as seen in previous components
const STATIC_API_URL = "https://pay.winco.cc/gateapi/v3/route.php"

const TransactionPage = () => {
  const COLORS = useColors();
  const [activeTab, setActiveTab] = useState("Deposit")
  const [filter, setFilter] = useState("All")
  const authSecretKey = sessionStorage.getItem("auth_secret_key")
  const userId = sessionStorage.getItem("account_id")
  const [depositRecords, setDepositRecords] = useState([])
  const [withdrawRecords, setWithdrawRecords] = useState([])
  const [toast, setToast] = useState(null)

  useEffect(() => {
    fetchDepositRecords()
    fetchWithdrawRecords()
  }, [])

  const fetchWithdrawRecords = async () => {
    if (!authSecretKey) return
    try {
      const response = await fetch(`${STATIC_API_URL}?USER_ID=${userId}&PAGE_NUM=1`, {
        method: "GET",
        headers: { Route: "route-withdraw-records", AuthToken: authSecretKey },
      })
      const result = await response.json()
      setWithdrawRecords(result.data || [])
    } catch (error) {
      console.error("Fetch error", error)
    }
  }

  const fetchDepositRecords = async () => {
    if (!authSecretKey) return
    try {
      const response = await fetch(`http://localhost/router/?USER_ID=${userId}&PAGE_NUM=1`, {
        method: "GET",
        headers: { Route: "route-recharge-records", AuthToken: authSecretKey },
      })
      const result = await response.json()
      setDepositRecords(result.data || [])
    } catch (error) {
      console.error("Fetch error", error)
    }
  }

  const mergeTransactions = (dep, wit) => {
    return [
      ...dep.map((r) => ({
        id: r.r_uniq_id,
        balance: r.r_amount,
        category: "Deposit",
        date: r.r_date,
        type: r.r_mode,
        details: r.r_details,
        time: r.r_time,
        orderNumber: r.r_uniq_id,
        remark: r.r_remark,
        status: r.r_status,
      })),
      ...wit.map((r) => ({
        id: r.w_uniq_id,
        balance: r.w_amount,
        category: "Withdrawal",
        date: r.w_date,
        time: r.w_time,
        orderNumber: r.w_uniq_id,
        remark: r.w_remark,
        status: r.w_status,
      })),
    ]
  }

  const transactionsData = mergeTransactions(depositRecords, withdrawRecords)
  const filteredTransactions = transactionsData.filter(
    (t) => (t.category === activeTab) && (filter === "All" || t.status.toLowerCase() === filter.toLowerCase())
  ).sort((a, b) => new Date(b.date + " " + b.time) - new Date(a.date + " " + a.time))

  return (
    <div className="text-black dark:text-white w-[98%] max-w-5xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-3xl relative mb-2"
      style={{ backgroundColor: COLORS.bg2, color: COLORS.text }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Refined Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 flex items-center justify-between relative z-10 bg-white/[0.02]">
        <div className="flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl flex items-center justify-center bg-brand shadow-lg text-black dark:text-white text-lg">
            <FaHistory />
          </div>
          <div>
            <h2 className="text-xl font-black uppercase tracking-tight" style={{ fontFamily: FONTS.head }}>
              Account <span className="text-brand">History</span>
            </h2>
            <div className="flex items-center gap-2 mt-1">
              <span className="text-[10px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Statement Logs</span>
            </div>
          </div>
        </div>
      </div>

      {/* Custom Toast */}
      {toast && (
        <div className="fixed top-6 right-6 z-[100] animate-fadeIn">
          <div className={`flex items-center p-4 rounded-xl border shadow-2xl backdrop-blur-xl ${toast.type === "success" ? "bg-black/10 dark:bg-black/90 border-green-500/30 text-black dark:text-white" : "bg-black/10 dark:bg-black/90 border-red-500/30 text-black dark:text-white"}`}>
            {toast.type === "success" ? <FaCheckCircle className="text-green-500 mr-3 text-xl" /> : <FaExclamationTriangle className="text-red-500 mr-3 text-xl" />}
            <span className="text-xs font-black uppercase tracking-wide">{toast.message}</span>
            <button onClick={() => setToast(null)} className="ml-6 text-xl opacity-30 hover:opacity-100">&times;</button>
          </div>
        </div>
      )}

      {/* Controls Section */}
      <div className="p-4 md:p-5 space-y-4 relative z-10">

        {/* Category Tabs */}
        <div className="flex bg-gray-100 dark:bg-black p-1 rounded-xl border border-black/5 dark:border-white/5 max-w-md mx-auto">
          {["Deposit", "Withdrawal"].map((tab) => (
            <button key={tab} onClick={() => setActiveTab(tab)}
              className={`flex-1 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-300 ${activeTab === tab ? "bg-brand text-black dark:text-white shadow-lg" : "text-black/20 dark:text-white/20 hover:text-black/50 dark:text-white/50"}`}>
              {tab}
            </button>
          ))}
        </div>

        {/* Status Filter Cards */}
        <div className="flex flex-wrap justify-center gap-1.5">
          {["All", "Success", "Processing", "Rejected"].map((f) => (
            <button key={f} onClick={() => setFilter(f)}
              className={`px-4 py-1.5 rounded-lg border text-[8px] font-black uppercase tracking-widest transition-all ${filter === f ? "border-brand/40 bg-brand/10 text-brand" : "border-black/5 dark:border-white/5 bg-white/[0.03] text-black/20 dark:text-white/20"}`}>
              {f}
            </button>
          ))}
        </div>

        {/* Transaction List - BALANCED CARDS */}
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-4 pb-4">
          {filteredTransactions.length > 0 ? (
            filteredTransactions.map((tx) => (
              <div key={tx.id} className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-2xl p-4 relative group hover:border-brand/20 transition-all duration-300">
                <div className="flex justify-between items-start mb-3 relative z-10">
                  <div className="flex items-center gap-3">
                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center text-lg ${tx.category === 'Deposit' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'}`}>
                      {tx.category === 'Deposit' ? <FaWallet /> : <FaExchangeAlt />}
                    </div>
                    <div>
                      <p className="text-lg font-black text-black dark:text-white" style={{ fontFamily: FONTS.head }}>₹{tx.balance}</p>
                      <p className="text-[9px] font-bold uppercase text-black/30 dark:text-white/30">{tx.date} • {tx.time}</p>
                    </div>
                  </div>

                  <div className={`px-2 py-1 rounded-md text-[7px] font-black uppercase tracking-widest flex items-center gap-1 border ${tx.status.toLowerCase() === 'success' ? 'bg-green-500/10 text-green-500 border-green-500/20' :
                      tx.status.toLowerCase() === 'processing' ? 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' :
                        'bg-red-500/10 text-red-500 border-red-500/20'
                    }`}>
                    {tx.status.toLowerCase() === 'processing' ? <FaClock className="animate-spin" /> :
                      tx.status.toLowerCase() === 'success' ? <FaCheckCircle /> : <FaExclamationTriangle />}
                    {tx.status}
                  </div>
                </div>

                {/* Footer of Card */}
                <div className="flex items-center justify-between pt-4 border-t border-white/[0.03] relative z-10">
                  <div className="flex flex-col gap-0.5">
                    <span className="text-[7px] text-black/20 dark:text-white/20 font-black uppercase tracking-widest">Order ID</span>
                    <span className="text-[9px] font-mono font-bold text-black/40 dark:text-white/40">{tx.orderNumber.substring(0, 20)}...</span>
                  </div>
                  <button onClick={() => { navigator.clipboard.writeText(tx.orderNumber); setToast({ message: "Order ID Copied", type: "success" }); setTimeout(() => setToast(null), 3000); }}
                    className="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-black/40 dark:text-white/40 hover:bg-brand/20 hover:text-brand transition-all border border-black/5 dark:border-white/5 shadow-xl">
                    <FaClipboard className="text-[12px]" />
                  </button>
                </div>
              </div>
            ))
          ) : (
            <div className="col-span-full py-24 text-center space-y-4">
              <div className="w-20 h-20 bg-white/[0.02] rounded-full flex items-center justify-center mx-auto border-2 border-dashed border-black/5 dark:border-white/5 opacity-20">
                <FaSearch className="text-3xl" />
              </div>
              <p className="text-[10px] font-black uppercase text-black/20 dark:text-white/20 tracking-[1em]">No records found</p>
            </div>
          )}
        </div>

      </div>

      {/* Footer Info */}
      <div className="p-4 md:p-5 bg-brand/5 border-t border-black/5 dark:border-white/5 flex items-center gap-3 relative z-10">
        <FaInfoCircle className="text-brand text-sm flex-shrink-0" />
        <p className="text-[8px] text-black/40 dark:text-white/40 uppercase font-black tracking-widest leading-relaxed">
          Record updates are real-time. Contact support if a transaction is missing after 5 minutes.
        </p>
      </div>

      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Statement Node</p>
      </div>
    </div>
  )
}

export default TransactionPage
