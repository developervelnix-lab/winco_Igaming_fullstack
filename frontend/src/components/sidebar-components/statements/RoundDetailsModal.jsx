import React from 'react';
import { X, Info, Receipt, Trophy, Calendar, Hash, Shield, Monitor, PlayCircle } from 'lucide-react';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const RoundDetailsModal = ({ isOpen, onClose, transaction }) => {
  const COLORS = useColors();

  if (!isOpen || !transaction) return null;

  const DetailItem = ({ label, value, icon: Icon }) => (
    <div className="flex justify-between items-start py-3 border-b border-black/5 dark:border-white/5 last:border-0 group">
      <div className="flex items-center gap-2">
        {Icon && <Icon size={14} className="text-black/30 dark:text-white/30 group-hover:text-brand transition-colors" />}
        <span className="text-[10px] font-black uppercase tracking-widest text-black/40 dark:text-white/40" style={{ fontFamily: FONTS.ui }}>
          {label}
        </span>
      </div>
      <span className="text-xs font-bold text-black dark:text-white text-right max-w-[150px] break-all" style={{ fontFamily: FONTS.ui }}>
        {value || '—'}
      </span>
    </div>
  );

  const SectionTitle = ({ title, icon: Icon }) => (
    <div className="flex items-center gap-3 mb-6 pb-2 border-b-2 border-brand/20">
      <div className="w-8 h-8 rounded-lg flex items-center justify-center bg-brand/10 text-brand">
        <Icon size={18} />
      </div>
      <h3 className="text-sm font-black uppercase tracking-[0.2em] text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
        {title}
      </h3>
    </div>
  );

  return (
    <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
      {/* Backdrop */}
      <div 
        className="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm transition-opacity animate-fadeIn" 
        onClick={onClose} 
      />

      {/* Modal */}
      <div 
        className="relative w-[95%] max-w-6xl max-h-[95vh] overflow-hidden rounded-[2.5rem] border border-black/10 dark:border-white/10 shadow-3xl animate-scaleIn flex flex-col"
        style={{ backgroundColor: COLORS.bg2 }}
      >
        {/* Header */}
        <div className="p-6 md:p-8 border-b border-black/5 dark:border-white/5 flex items-center justify-between bg-white/[0.02] relative z-10">
          <div className="flex items-center gap-4">
            <div className="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-black dark:text-white text-xl"
              style={{ background: COLORS.brandGradient }}>
              <Receipt />
            </div>
            <div>
              <h2 className="text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
                Round Details <span style={{ color: COLORS.brand }}>({transaction.r_provider || 'Game'})</span>
              </h2>
              <p className="text-[10px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Transaction & Settlement Report</p>
            </div>
          </div>
          <button 
            onClick={onClose}
            className="w-10 h-10 rounded-full bg-black/5 dark:bg-white/5 flex items-center justify-center text-black/40 dark:text-white/40 hover:bg-red-500 hover:text-white transition-all transform hover:rotate-90"
          >
            <X size={20} />
          </button>
        </div>

        {/* Content */}
        <div className="p-4 md:p-10 overflow-y-auto scrollbar-hide relative z-10">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            
            {/* Round Details */}
            <div className="space-y-2">
              <SectionTitle title="Round Details" icon={Info} />
              <div className="bg-black/10 dark:bg-black/40 rounded-2xl p-5 shadow-inner">
                <DetailItem label="Provider" value={transaction.r_provider || 'Evolution'} icon={Shield} />
                <DetailItem label="Round Id" value={transaction.r_round_id || '189db1bd9917785d1450b1dc'} icon={Hash} />
                <DetailItem label="Game Type" value={transaction.r_match_name || 'roulette'} icon={PlayCircle} />
                <DetailItem label="Table Name" value={transaction.r_table_name || 'Speed Roulette'} icon={Monitor} />
                <DetailItem label="Started At" value={`${transaction.r_date} ${transaction.r_time}`} icon={Calendar} />
                <DetailItem label="Settled At" value={transaction.r_settled_at || `${transaction.r_date} ${transaction.r_time}`} icon={Calendar} />
                <DetailItem label="Status" value={transaction.r_match_status || 'Resolved'} icon={Info} />
                <DetailItem label="Bets TransactionId" value={transaction.r_uniq_id} icon={Hash} />
                <DetailItem label="Bets PlayMode" value="RealMoney" icon={PlayCircle} />
                <DetailItem label="Bets Channel" value="Desktop/Mobile" icon={Monitor} />
              </div>
            </div>

            {/* Bet Details */}
            <div className="space-y-2">
              <SectionTitle title="Bet Details" icon={Receipt} />
              <div className="bg-black/10 dark:bg-black/40 rounded-2xl p-5 shadow-inner">
                <DetailItem label="Code" value={transaction.r_selection || 'ROU_0Green'} icon={Hash} />
                <DetailItem label="Stake" value={`₹${transaction.r_match_bet || transaction.r_match_amount}`} icon={Receipt} />
                <DetailItem label="Payout" value={`₹${transaction.r_match_profit || '0'}`} icon={Trophy} />
                <DetailItem label="PlacedOn" value={`${transaction.r_date} ${transaction.r_time}`} icon={Calendar} />
                <DetailItem label="Description" value={transaction.r_match_name} icon={Info} />
                <DetailItem label="TransactionId" value={transaction.r_uniq_id} icon={Hash} />
              </div>
            </div>

            {/* Results */}
            <div className="space-y-2">
              <SectionTitle title="Results" icon={Trophy} />
              <div className="bg-black/5 dark:bg-white/5 rounded-2xl p-5 shadow-inner">
                <DetailItem label="Outcome Number" value={transaction.r_outcome_number || '0'} icon={Hash} />
                <DetailItem label="Outcome Type" value={transaction.r_outcome_type || 'Success'} icon={Info} />
                <DetailItem label="Outcome Color" value={transaction.r_outcome_color || 'Green'} icon={Info} />
                <DetailItem label="Wager" value={`₹${transaction.r_match_bet || transaction.r_match_amount}`} icon={Receipt} />
                <DetailItem label="Payout" value={`₹${transaction.r_match_profit || '0'}`} icon={Trophy} />
                
                <div className="mt-10 pt-8 border-t border-black/10 dark:border-white/10 text-center">
                  <div className={`inline-flex items-center gap-4 px-8 py-4 rounded-full font-black uppercase tracking-[0.2em] text-[10px] ${
                    transaction.r_match_status === 'loss' ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-green-500/10 text-green-500 border border-green-500/20'
                  }`}>
                    {transaction.r_match_status === 'loss' ? 'Net Loss' : 'Net Profit'}: 
                    <span className="text-xl" style={{ fontFamily: FONTS.head }}>₹{transaction.r_match_profit}</span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        {/* Footer */}
        <div className="p-6 md:p-8 bg-brand/5 border-t border-black/5 dark:border-white/5 flex items-center justify-center relative z-10">
          <p className="text-[9px] text-black/30 dark:text-white/30 uppercase font-black tracking-[0.3em]">
            Official Transaction Record • Verification Code: {transaction.r_uniq_id?.substring(0, 12).toUpperCase()}
          </p>
        </div>
      </div>
    </div>
  );
};

export default RoundDetailsModal;
