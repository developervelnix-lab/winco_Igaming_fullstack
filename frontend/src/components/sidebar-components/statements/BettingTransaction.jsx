"use client";

import { useState, useEffect } from "react";
import { FaHistory, FaSearch, FaCheckCircle, FaExclamationTriangle, FaChartLine } from "react-icons/fa";
import { API_URL } from "@/utils/constants";
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';
import RoundDetailsModal from './RoundDetailsModal';

const BettingTransactionPage = () => {
  const COLORS = useColors();
  const [activeFilter, setActiveFilter] = useState("Last 7 Days");
  const [transactions, setTransactions] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedTx, setSelectedTx] = useState(null);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const authSecretKey = sessionStorage.getItem("auth_secret_key");
  const userId = sessionStorage.getItem("account_id");

  const fetchGameData = async (userId) => {
    try {
      const url = `${API_URL}?USER_ID=${userId}`;
      const response = await fetch(url, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Route: "route-mygame-records",
          AuthToken: authSecretKey,
        },
      });
      const result = await response.json();
      return result.data;
    } catch (error) {
      console.error("Error fetching game data", error);
    }
  };

  useEffect(() => {
    if (userId) {
      fetchGameData(userId).then((data) => {
        if (data) setTransactions(data);
      });
    }
  }, [userId]);

  const handleFilterClick = (filter) => {
    setActiveFilter(filter);
  };

  const filteredTransactions = transactions.filter((t) =>
    t.r_match_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    (t.r_selection && t.r_selection.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  return (
    <div className="w-[98%] max-w-6xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-2"
      style={{ backgroundColor: COLORS.bg2, color: COLORS.text }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10 bg-white/[0.02]">
        <div className="flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-lg"
            style={{ background: COLORS.brandGradient }}>
            <FaChartLine />
          </div>
          <div>
            <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight" style={{ fontFamily: FONTS.head }}>
              Betting <span style={{ color: COLORS.brand }}>P&L</span>
            </h2>
            <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Profit & Loss Table</span>
          </div>
        </div>

        <div className="flex flex-col md:flex-row items-center gap-3">
          {/* Search Input */}
          <div className="relative w-full md:w-56 group">
            <FaSearch className="absolute left-3 top-1/2 -translate-y-1/2 text-black/40 dark:text-white/20 group-focus-within:text-brand transition-colors text-[10px]" />
            <input 
              type="text"
              placeholder="SEARCH GAME..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-8 pr-3 py-2 rounded-lg bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-black dark:text-white text-[9px] font-black uppercase tracking-widest placeholder-black/30 dark:placeholder-white/30 focus:outline-none focus:border-brand/40 focus:ring-1 focus:ring-brand/20 transition-all shadow-inner"
              style={{ fontFamily: FONTS.ui }}
            />
          </div>

          {/* Filter Buttons */}
          <div className="flex flex-wrap gap-1.5">
            {["Last 7 Days", "Last 14 Days", "Last 28 Days"].map((filter) => (
              <button
                key={filter}
                onClick={() => handleFilterClick(filter)}
                className={`px-3 py-2 rounded-lg border text-[7px] font-black uppercase tracking-widest transition-all duration-300 ${
                  activeFilter === filter
                    ? "border-brand/40 text-black dark:text-white shadow-lg"
                    : "border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/[0.03] text-black/40 dark:text-white/30 hover:bg-black/10 dark:hover:bg-white/10"
                }`}
                style={activeFilter === filter ? { background: COLORS.brandGradient } : {}}
              >
                {filter}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Table Content */}
      <div className="p-4 md:p-5 relative z-10 overflow-x-auto scrollbar-hide">
        {filteredTransactions.length > 0 ? (
          <div className="min-w-[800px]">
            <table className="w-full text-left border-separate border-spacing-y-1.5">
              <thead>
                <tr className="text-black/30 dark:text-white/30 text-[8px] font-black uppercase tracking-widest">
                  <th className="px-4 py-3">S.No</th>
                  <th className="px-4 py-3">Game Name</th>
                  <th className="px-4 py-3">Provider</th>
                  <th className="px-4 py-3">Bet Amount</th>
                  <th className="px-4 py-3">Date & Time</th>
                  <th className="px-4 py-3">Selection</th>
                  <th className="px-4 py-3 text-right">Result</th>
                </tr>
              </thead>
              <tbody className="space-y-2">
                {filteredTransactions.map((transaction, index) => (
                  <tr 
                    key={index} 
                    className="bg-black/10 dark:bg-black/40 border border-black/5 dark:border-white/5 hover:bg-white/[0.02] transition-colors group"
                  >
                    <td className="px-4 py-3 rounded-l-xl border-y border-l border-black/5 dark:border-white/5">
                      <span className="text-[9px] font-black text-black/40 dark:text-white/40">{index + 1}</span>
                    </td>
                    <td className="px-4 py-3 border-y border-black/5 dark:border-white/5">
                      <div className="flex flex-col">
                        <button 
                          onClick={() => { setSelectedTx(transaction); setIsModalOpen(true); }}
                          className="text-[11px] font-black text-black dark:text-white uppercase tracking-wide hover:text-brand transition-colors text-left group-hover:translate-x-1 duration-300" 
                          style={{ fontFamily: FONTS.head }}
                        >
                          {transaction.r_match_name}
                        </button>
                        <span className="text-[7px] font-bold text-black/20 dark:text-white/20 uppercase">Match Record</span>
                      </div>
                    </td>
                    <td className="px-4 py-3 border-y border-black/5 dark:border-white/5">
                      <span className="text-[9px] font-black text-black/40 dark:text-white/40 uppercase tracking-widest">
                        {transaction.r_provider || "Standard"}
                      </span>
                    </td>
                    <td className="px-4 py-3 border-y border-black/5 dark:border-white/5">
                      <span className="text-[11px] font-black text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
                        ₹{transaction.r_match_bet || transaction.r_match_amount}
                      </span>
                    </td>
                    <td className="px-4 py-3 border-y border-black/5 dark:border-white/5">
                      <div className="flex flex-col">
                        <span className="text-[9px] font-bold text-black/60 dark:text-white/60">{transaction.r_date}</span>
                        <span className="text-[8px] font-bold text-black/20 dark:text-white/20">{transaction.r_time}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3 border-y border-black/5 dark:border-white/5">
                      <span className="text-[9px] font-black text-brand italic uppercase tracking-wider">
                        {transaction.r_selection || transaction.r_match_name.split('vs')[1] || "—"}
                      </span>
                    </td>
                    <td className="px-4 py-3 rounded-r-xl border-y border-r border-black/5 dark:border-white/5 text-right">
                      <div className="flex flex-col items-end">
                        <span className={`text-[8px] font-black uppercase tracking-widest mb-0.5 ${
                          transaction.r_match_status === "loss" ? "text-red-500" : "text-green-500"
                        }`}>
                          {transaction.r_match_status}
                        </span>
                        <span className={`text-sm font-black ${
                          transaction.r_match_status === "loss" ? "text-red-500" : "text-green-500"
                        }`} style={{ fontFamily: FONTS.head }}>
                          {transaction.r_match_status === "loss" ? "-" : "+"}₹{transaction.r_match_profit}
                        </span>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="py-24 text-center space-y-4">
            <div className="w-20 h-20 bg-white/[0.02] rounded-full flex items-center justify-center mx-auto border-2 border-dashed border-black/5 dark:border-white/5 opacity-20">
              <FaSearch className="text-3xl" />
            </div>
            <p className="text-[10px] font-black uppercase text-black/20 dark:text-white/20 tracking-[0.5em]">No betting records found</p>
          </div>
        )}
      </div>

      {/* Footer */}
      <div className="p-4 md:p-5 bg-brand/5 border-t border-black/5 dark:border-white/5 flex items-center gap-3 relative z-10">
        <FaHistory className="text-brand text-sm flex-shrink-0" />
        <p className="text-[8px] text-black/40 dark:text-white/40 uppercase font-black tracking-widest leading-relaxed">
          Table data is synchronized real-time. Contact support if any discrepancy is found.
        </p>
      </div>

      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">P&L Table Node</p>
      </div>

      <RoundDetailsModal 
        isOpen={isModalOpen} 
        onClose={() => setIsModalOpen(false)} 
        transaction={selectedTx} 
      />
    </div>
  );
};

export default BettingTransactionPage;
