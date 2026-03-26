import React from 'react';
import { FaShieldAlt } from 'react-icons/fa';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';

const ExclusionPolicy = () => {
  const COLORS = useColors();
  const questions = [
    {
      q: "What is a Self-Exclusion?",
      a: "Self-exclusion is a facility that the Site offers to help those customers who feel that their gambling is out of control and want us to help them stop. By entering into a self-exclusion agreement with the Site, you will be prevented from using your Account (as defined in the terms and conditions) for a specific period, as determined by you, of between 6 months and 5 years."
    },
    {
      q: "How to self-exclude from the Site",
      a: "If at any time you should wish to exclude yourself from use of the Site, you must submit this request by WhatsApp. Please inform us of the period for which you wish to self-exclude. The minimum is 6 months and the maximum is 5 years. If you request self-exclusion but do not specify a period, we will exclude you for the minimum period of six months."
    },
    {
      q: "How soon after requesting will it be activated?",
      a: "We will endeavour to apply your exclusion as soon as practically possible. Normally, we will be able to reset your password to prevent you from accessing the Site within 24 hours of your request."
    },
    {
      q: "What happens if I self-exclude?",
      a: null,
      list: [
        "Prevent any marketing material being forwarded to you;",
        "Remove you from any marketing databases operated by us;",
        "Suspend your activity by cancelling your ability to access the Site for the requested period;",
        "Permanently close your Customer Account if instructed to do so, and return all funds owed to you."
      ]
    },
    {
      q: "Can I re-activate my Account during the self-exclusion period?",
      a: "Accounts that have been self-excluded cannot be reactivated under any circumstances until the expiry of the self-exclusion period. During this period, you must not attempt to re-open any existing Account(s), seek to open any new Accounts, or place bets through any other customer's Account."
    },
    {
      q: "Can I re-activate my Account after the period ends?",
      a: "At the end of the self-exclusion period, you must contact us in person and confirm such intention in writing. If it is decided (in the Site's absolute discretion) to permit you to re-open your Account/open a new Account, you should be aware that a 24-hour waiting period will be imposed before the Account is available for use."
    }
  ];

  return (
    <div className="w-[96%] max-w-3xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative mb-6"
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
          <FaShieldAlt />
        </div>
        <div>
          <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Self <span style={{ color: COLORS.brand }}>Exclusion</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Player Protection</span>
        </div>
      </div>

      {/* Q&A Content */}
      <div className="p-3 md:p-5 space-y-3 relative z-10">
        {questions.map((item, i) => (
          <div key={i} className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5 hover:border-brand/20 transition-all duration-300">
            <h3 className="text-[11px] md:text-xs font-black uppercase tracking-widest mb-2" style={{ fontFamily: FONTS.head, color: COLORS.brand }}>
              {item.q}
            </h3>
            {item.a && (
              <p className="text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
                {item.a}
              </p>
            )}
            {item.list && (
              <ul className="space-y-1.5 mt-2">
                {item.list.map((li, j) => (
                  <li key={j} className="flex items-start gap-2 text-black/50 dark:text-white/50 text-[11px] md:text-xs" style={{ fontFamily: FONTS.ui }}>
                    <span className="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style={{ backgroundColor: COLORS.brand }}></span>
                    {li}
                  </li>
                ))}
              </ul>
            )}
          </div>
        ))}
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Legal</p>
      </div>
    </div>
  );
};

export default ExclusionPolicy;
