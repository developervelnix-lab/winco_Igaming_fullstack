import React from "react";
import { FaLock } from "react-icons/fa";
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';
import Navbar from "@/components/navbar/Navbar";

const PrivacyPolicy = () => {
  const COLORS = useColors();
  const sections = [
    {
      title: "About Us",
      content: "We are the flagship brand, offering Our Services to You, wherein You can also participate in various Contests hosted on Our Platform. Any person utilizing the Platform or any of its features, including participation in various Contests, shall be bound by the terms of this Privacy Policy."
    },
    {
      title: "Information We Collect",
      content: "We respect the privacy of our Users and are committed to protecting it. To offer an enriching and holistic internet experience, we collect user information as follows:",
      list: ["Information supplied by Users.", "Information automatically tracked during navigation."]
    },
    {
      title: "Purpose and Usage",
      content: "To avail certain features on the Platform, Users may be required to provide the following information:",
      list: ["Username", "Email Address", "Date Of Birth", "State", "Government ID (Aadhaar card, driving license, or voter ID)"],
      extra: "Additionally, we may collect information related to your device, operating system, network, and location for enhancing our services."
    },
    {
      title: "Disclosure & Sharing",
      content: "We may share your information with our affiliates, group entities, or third-party service providers for data analytics, storage, and improving our services. However, we take necessary measures to ensure your data is protected."
    },
    {
      title: "Use of Cookies",
      content: "We use cookies and similar electronic tools to collect information for a better user experience. Cookies help us understand user preferences and improve our services accordingly."
    },
    {
      title: "Security",
      content: "All gathered information is securely stored in a controlled database, with restricted access. However, while we implement robust security measures, no system is entirely impenetrable."
    },
    {
      title: "Data Retention & Account Deletion",
      content: "Your personal information will be retained only as long as necessary for legitimate business purposes or legal requirements. You may request account and data deletion by contacting our support team."
    },
    {
      title: "Contact Us",
      content: "If you have any queries regarding this Privacy Policy, feel free to contact us at the details provided in the footer."
    }
  ];

  return (
    <>
      <Navbar />
      <div className="pt-[140px] md:pt-[160px] px-2 pb-10" style={{ backgroundColor: COLORS.bg }}>
        <div className="w-[96%] max-w-4xl mx-auto overflow-hidden rounded-3xl border border-black/10 dark:border-white/10 shadow-2xl relative"
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
              <FaLock />
            </div>
            <div>
              <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
                Privacy <span style={{ color: COLORS.brand }}>Policy</span>
              </h2>
              <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Data Protection</span>
            </div>
          </div>

          {/* Content */}
          <div className="p-3 md:p-5 space-y-3 relative z-10">
            <p className="text-black/40 dark:text-white/40 text-[10px] leading-relaxed px-2" style={{ fontFamily: FONTS.ui }}>
              By using any part of the Platform, You consent to the collection, use, disclosure, and transfer of Your information for the purposes outlined in this Privacy Policy.
            </p>
 
            {sections.map((section, i) => (
              <div key={i} className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5 hover:border-brand/20 transition-all duration-300">
                <h3 className="text-[11px] md:text-xs font-black uppercase tracking-widest mb-2" style={{ fontFamily: FONTS.head, color: COLORS.brand }}>
                  {section.title}
                </h3>
                <p className="text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
                  {section.content}
                </p>
                {section.list && (
                  <ul className="mt-2.5 space-y-1.5">
                    {section.list.map((item, j) => (
                      <li key={j} className="flex items-start gap-2.5 text-black/40 dark:text-white/40 text-[11px] md:text-xs" style={{ fontFamily: FONTS.ui }}>
                        <span className="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" style={{ backgroundColor: COLORS.brand }}></span>
                        {item}
                      </li>
                    ))}
                  </ul>
                )}
                {section.extra && (
                  <p className="text-black/40 dark:text-white/40 text-[11px] md:text-xs leading-relaxed mt-2.5" style={{ fontFamily: FONTS.ui }}>
                    {section.extra}
                  </p>
                )}
              </div>
            ))}
          </div>

          {/* Footer */}
          <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
            <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Privacy</p>
          </div>
        </div>
      </div>
    </>
  );
};

export default PrivacyPolicy;
