import React, { useState } from 'react';
import { FaGavel, FaChevronDown, FaChevronUp } from 'react-icons/fa';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const Section = ({ title, children, defaultOpen = false }) => {
  const COLORS = useColors();
  const [open, setOpen] = useState(defaultOpen);
  return (
    <div className="border border-black/5 dark:border-white/5 rounded-xl overflow-hidden transition-all duration-300 hover:border-black/10 dark:border-white/10">
      <button
        className="w-full flex items-center justify-between p-3.5 md:p-4 bg-white/[0.02] text-left group"
        onClick={() => setOpen(!open)}
      >
        <span className="text-[11px] md:text-xs font-black uppercase tracking-widest text-black/70 dark:text-white/70 group-hover:text-black dark:text-white transition-colors" style={{ fontFamily: FONTS.head }}>
          {title}
        </span>
        {open ? <FaChevronUp className="text-brand text-[10px]" /> : <FaChevronDown className="text-black/20 dark:text-white/20 text-[10px]" />}
      </button>
      {open && (
        <div className="p-3.5 md:p-5 space-y-2.5 border-t border-black/5 dark:border-white/5 text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
          {children}
        </div>
      )}
    </div>
  );
};

const RulesAndRegulation = () => {
  const COLORS = useColors();
  return (
    <div className="w-[96%] max-w-4xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
      style={{ backgroundColor: COLORS.bg2 }}>

      {/* Background Glows */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute top-0 right-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-brand/30 blur-[100px]"></div>
      </div>

      {/* Header */}
      <div className="p-4 md:p-6 border-b border-black/5 dark:border-white/5 flex items-center gap-4 relative z-10 bg-white/[0.02]">
        <div className="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg text-black dark:text-white text-lg"
          style={{ background: COLORS.brandGradient }}>
          <FaGavel />
        </div>
        <div>
          <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Rules & <span style={{ color: COLORS.brand }}>Regulations</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Legal Compliance</span>
        </div>
      </div>

      {/* Content Sections */}
      <div className="p-3 md:p-5 space-y-3 relative z-10">

        <Section title="Part A — Introduction" defaultOpen={true}>
          <p><strong className="text-black/80 dark:text-white/80">Use and Interpretation</strong></p>
          <p>These Rules and Regulations ("Rules") are part of the terms and conditions.</p>
          <p>The Rules apply to all bets placed on this online betting platform.</p>
          <p>The Rules consist of the following:</p>
          <ul className="list-disc pl-5 space-y-1 text-black/40 dark:text-white/40">
            <li>Introduction Section (Part A);</li>
            <li>The General Rules (Set out in Part B below); and</li>
            <li>The Specific Sports Rules (set out in Part C below - these apply to certain sports).</li>
          </ul>
          <p>The General Rules apply to all bets unless stated otherwise in the Specific Sports Rules. If there is any inconsistency between the Specific Sports Rules and the General Rules, the Specific Sports Rules shall prevail.</p>
          <p>The rules governing how markets are offered, managed and/or settled are not the same for every market on each product. Customers must ensure that they familiarise themselves with the relevant rules that apply to the bets that they place on the Site.</p>
          <p><strong className="text-black/80 dark:text-white/80">Customer Responsibility</strong></p>
          <ul className="list-disc pl-5 space-y-1 text-black/40 dark:text-white/40">
            <li>Customers should make themselves aware of all of the Rules affecting any market on which they wish to place a bet.</li>
            <li>In particular, customers who use the "one-click" option to place bets are solely responsible for their actions.</li>
          </ul>
        </Section>

        <Section title="Part B — General Rules">
          <p><strong className="text-black/80 dark:text-white/80">Matters beyond the Site's reasonable control</strong></p>
          <p>The Site is not liable for any loss or damage you may suffer because of any: Act of God, Power cut, Trade or labour dispute, Act of any government or authority, Obstruction of telecommunication services, or any other delay caused by a third party.</p>
          <p>In such an event, the Site reserves the right to cancel or suspend access without incurring any liability.</p>

          <p><strong className="text-black/80 dark:text-white/80">Managing markets In-Play</strong></p>
          <p>If a market is not scheduled to be turned in-play but the Site fails to suspend the market at the relevant time, bets matched after the scheduled off time will be void.</p>
          <p>The Site aims to use its reasonable endeavours to suspend in-play markets at the start of and at the end of the event. Customers are responsible for managing their in-play bets at all times.</p>

          <p><strong className="text-black/80 dark:text-white/80">Results and Market Settlement</strong></p>
          <p>Markets will be settled in accordance as set out in the Specific Sports Rules. Where not specified, markets will be settled on the official result of the relevant governing body.</p>
          <p>In the event of any uncertainty about any result, the Site reserves the right to suspend settlement for an unlimited period.</p>

          <p><strong className="text-black/80 dark:text-white/80">Non-runners, Withdrawals and Disqualifications</strong></p>
          <p>If a team or competitor is disqualified, withdraws or forfeits after starting an event they will be deemed a loser providing at least one other team or competitor completes the event.</p>

          <p><strong className="text-black/80 dark:text-white/80">Abandonments, Cancellations, Postponements</strong></p>
          <p>If the event is not completed within three days after the scheduled completion date, then all bets on markets for this event will be void, except for bets on any markets that have been unconditionally determined.</p>

          <p><strong className="text-black/80 dark:text-white/80">Multiple Accounts</strong></p>
          <p>Customers are not permitted to hold multiple accounts. The Site reserves the right to void bets and/or withhold winnings from customers operating multiple accounts.</p>
          
          <p><strong className="text-black/80 dark:text-white/80">VPN and Proxy Usage</strong></p>
          <p>Customers using VPN or proxy servers to mask location are liable to having bets invalidated.</p>

          <p><strong className="text-black/80 dark:text-white/80">Integrity</strong></p>
          <p>The Site reserves the right to void any bets under integrity investigation. Cheating of any kind is not allowed.</p>
        </Section>

        <Section title="Part C — Cricket Rules">
          <p><strong className="text-black/80 dark:text-white/80">General</strong></p>
          <p>If a ball is not bowled during a competition, series or match then all bets will be void except for those on any market that has been unconditionally determined.</p>
          <p>If a match is shortened by weather, all bets will be settled according to the official result (including Duckworth Lewis method).</p>

          <p><strong className="text-black/80 dark:text-white/80">Test Matches</strong></p>
          <p>If a match starts but is later abandoned for any reason other than weather, the Site reserves the right to void all bets except for markets that have been unconditionally determined.</p>

          <p><strong className="text-black/80 dark:text-white/80">Limited Overs</strong></p>
          <p>If a match is declared "No Result", bets will be void on all markets except for unconditionally determined markets. The Site will void all Super Over bets in the event of a tied Super Over.</p>

          <p><strong className="text-black/80 dark:text-white/80">Sessions/Innings/Player Runs</strong></p>
          <p>All session/innings/player runs are based on Haar-Jeet odds format. In the event of rain reduced innings, bets will be settled accordingly. Extras and penalty runs will be included except slow over rate penalties.</p>

          <p><strong className="text-black/80 dark:text-white/80">Exchange Runs</strong></p>
          <p>Bets are placed in an exchange and matched at the requested run line or better. All exchange runs are based on decimal odds format at 2.00 odds.</p>
        </Section>

        <Section title="Part C — Soccer Rules">
          <p><strong className="text-black/80 dark:text-white/80">Not suspending at kick-off</strong></p>
          <p>If a market is scheduled to be turned in-play but the Site does not suspend at kick-off and the market is not turned in-play at any time during the match, all bets matched after the scheduled time of kick-off will be void.</p>

          <p><strong className="text-black/80 dark:text-white/80">Material Events</strong></p>
          <p>A "Material Event" means a goal being scored, a penalty being awarded or a player being sent off. If the Site does not suspend a market on time for a Material Event, it reserves the right to void bets unfairly matched after the event.</p>

          <p><strong className="text-black/80 dark:text-white/80">VAR Decisions</strong></p>
          <p>Where a Material Event is cancelled due to VAR, the Site will void all bets matched between the occurrence and the cancellation.</p>
        </Section>
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Legal</p>
      </div>
    </div>
  );
};

export default RulesAndRegulation;