import React, { useState, useRef } from 'react';
import { 
  FaEnvelope, FaMapMarkerAlt, FaWhatsapp, FaTelegramPlane, 
  FaInstagram, FaFacebookF, FaTwitter, FaPaperPlane, 
  FaCheckCircle, FaTicketAlt, FaPaperclip, FaTimes, FaUser 
} from 'react-icons/fa';
import { useColors } from '../../../hooks/useColors';
import { FONTS } from '../../../constants/theme';
import { useSite } from '../../../context/SiteContext';
import { API_URL } from '../../../utils/constants';

const ContactUs = () => {
  const COLORS = useColors();
  const { accountInfo } = useSite();
  const fileInputRef = useRef(null);
  
  const [formData, setFormData] = useState({ 
    name: '', 
    email: '', 
    subject: '', 
    message: '',
    priority: 'Medium',
    profile_id: ''
  });
  const [attachments, setAttachments] = useState([]);
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [ticketId, setTicketId] = useState('');

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    setAttachments([...attachments, ...files]);
  };

  const removeAttachment = (index) => {
    setAttachments(attachments.filter((_, i) => i !== index));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const userId = sessionStorage.getItem("account_id") || "guest";
      const authKey = sessionStorage.getItem("auth_secret_key") || "guest";
      
      const backendData = new FormData();
      Object.keys(formData).forEach(key => {
        backendData.append(key, formData[key]);
      });
      backendData.append("USER_ID", userId);
      
      attachments.forEach((file) => {
        backendData.append("attachments[]", file);
      });

      const response = await fetch(API_URL, {
        method: "POST",
        headers: {
          Route: "route-submit-ticket",
          AuthToken: authKey
          // Content-Type is set automatically for FormData with boundary
        },
        body: backendData
      });
      
      const result = await response.json();
      if (result.status_code === "success") {
        setTicketId(result.ticket_id || "TKT-" + Date.now());
        setSubmitted(true);
        setFormData({ 
          name: '', email: '', subject: '', 
          message: '', priority: 'Medium', profile_id: '' 
        });
        setAttachments([]);
      }
    } catch (err) {
      console.error("Ticket submission error:", err);
    } finally {
      setSubmitting(false);
    }
  };

  const socialIconMap = {
    'whatsapp': { icon: <FaWhatsapp />, color: "#25D366", prefix: "https://wa.me/" },
    'telegram': { icon: <FaTelegramPlane />, color: "#0088cc", prefix: "" },
    'instagram': { icon: <FaInstagram />, color: "#E1306C", prefix: "" },
    'facebook': { icon: <FaFacebookF />, color: "#1877F2", prefix: "" },
    'twitter': { icon: <FaTwitter />, color: "#1DA1F2", prefix: "" }
  };

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
          <FaEnvelope />
        </div>
        <div>
          <h2 className="text-xl md:text-2xl font-black uppercase tracking-tight text-black dark:text-white" style={{ fontFamily: FONTS.head }}>
            Contact <span style={{ color: COLORS.brand }}>Us</span>
          </h2>
          <span className="text-[9px] font-bold uppercase tracking-widest text-black/30 dark:text-white/30">Get In Touch</span>
        </div>
      </div>

      <div className="p-3 md:p-5 space-y-4 relative z-10">

        {/* Address & Social Links Section */}
        {(accountInfo?.service_address || (accountInfo?.service_social_links && accountInfo.service_social_links.length > 0)) && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">

            {/* Address Card */}
            {accountInfo?.service_address && (
              <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5 hover:border-brand/20 transition-all duration-300">
                <div className="flex items-center gap-2 mb-3">
                  <FaMapMarkerAlt style={{ color: COLORS.brand }} />
                  <h3 className="text-[11px] md:text-xs font-black uppercase tracking-widest" style={{ fontFamily: FONTS.head, color: COLORS.brand }}>
                    Our Address
                  </h3>
                </div>
                <p className="text-black/50 dark:text-white/50 text-[11px] md:text-xs leading-relaxed" style={{ fontFamily: FONTS.ui }}>
                  {accountInfo.service_address}
                </p>
              </div>
            )}

            {/* Social Links Card */}
            {accountInfo?.service_social_links && accountInfo.service_social_links.length > 0 && (
              <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5 hover:border-brand/20 transition-all duration-300">
                <h3 className="text-[11px] md:text-xs font-black uppercase tracking-widest mb-3" style={{ fontFamily: FONTS.head, color: COLORS.brand }}>
                  Connect With Us
                </h3>
                <div className="flex flex-wrap gap-2">
                  {accountInfo.service_social_links.map((link, i) => {
                    const platform = link.platform.toLowerCase();
                    let url = link.value;
                    const meta = socialIconMap[platform] || socialIconMap['telegram'];
                    if (platform === 'whatsapp' && !url.includes('wa.me')) {
                      url = meta.prefix + url.replace(/\s+/g, '');
                    } else if (!url.startsWith('http')) {
                      url = 'https://' + url;
                    }
                    return (
                      <a key={'contact_soc' + i} href={url} target="_blank" rel="noopener noreferrer"
                        className="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group border border-black/5 dark:border-white/5"
                        style={{ backgroundColor: `${meta.color}15` }}
                      >
                        <span className="text-base" style={{ color: meta.color }}>{meta.icon}</span>
                      </a>
                    );
                  })}
                </div>
              </div>
            )}
          </div>
        )}

        {/* Ticket Submission Form */}
        <div className="bg-gray-100 dark:bg-black border border-black/5 dark:border-white/5 rounded-xl p-4 md:p-5">
          <div className="flex items-center gap-2 mb-4">
            <FaTicketAlt style={{ color: COLORS.brand }} />
            <h3 className="text-[11px] md:text-xs font-black uppercase tracking-widest" style={{ fontFamily: FONTS.head, color: COLORS.brand }}>
              Submit A Support Ticket
            </h3>
          </div>

          {submitted ? (
            <div className="text-center py-8 space-y-4">
              <div className="w-16 h-16 rounded-full mx-auto flex items-center justify-center" style={{ backgroundColor: `${COLORS.brand}20` }}>
                <FaCheckCircle className="text-3xl" style={{ color: COLORS.brand }} />
              </div>
              <div>
                <h4 className="text-lg font-black text-black dark:text-white uppercase tracking-tight" style={{ fontFamily: FONTS.head }}>
                  Ticket Submitted!
                </h4>
                <p className="text-black/50 dark:text-white/50 text-xs mt-1" style={{ fontFamily: FONTS.ui }}>
                  Your ticket ID is <span className="font-bold" style={{ color: COLORS.brand }}>{ticketId}</span>
                </p>
                <p className="text-black/40 dark:text-white/40 text-[10px] mt-2" style={{ fontFamily: FONTS.ui }}>
                  Our support team will get back to you within 24 hours.
                </p>
              </div>
              <button
                onClick={() => setSubmitted(false)}
                className="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-black transition-all duration-300 hover:scale-105"
                style={{ background: COLORS.brandGradient }}
              >
                Submit Another Ticket
              </button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Your Name</label>
                  <input
                    type="text" name="name" required value={formData.name} onChange={handleChange}
                    className="w-full px-4 py-3 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white focus:border-brand/50 focus:outline-none transition-all"
                    style={{ fontFamily: FONTS.ui }}
                    placeholder="Enter your name"
                  />
                </div>
                <div>
                  <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Profile ID</label>
                  <div className="relative">
                    <input
                      type="text" name="profile_id" value={formData.profile_id} onChange={handleChange}
                      className="w-full px-4 py-3 pl-10 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white focus:border-brand/50 focus:outline-none transition-all"
                      style={{ fontFamily: FONTS.ui }}
                      placeholder="Optional User ID"
                    />
                    <FaUser className="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] text-black/30 dark:text-white/30" />
                  </div>
                </div>
                <div>
                  <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Email Address</label>
                  <input
                    type="email" name="email" required value={formData.email} onChange={handleChange}
                    className="w-full px-4 py-3 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white focus:border-brand/50 focus:outline-none transition-all"
                    style={{ fontFamily: FONTS.ui }}
                    placeholder="Enter your email"
                  />
                </div>
                <div>
                  <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Priority Level</label>
                  <select
                    name="priority" required value={formData.priority} onChange={handleChange}
                    className="w-full px-4 py-3 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white focus:border-brand/50 focus:outline-none transition-all appearance-none"
                    style={{ fontFamily: FONTS.ui }}
                  >
                    <option value="Low">Low Priority</option>
                    <option value="Medium">Medium Priority</option>
                    <option value="High">High Priority</option>
                    <option value="Urgent">Urgent</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Subject / Category</label>
                <select
                  name="subject" required value={formData.subject} onChange={handleChange}
                  className="w-full px-4 py-3 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white focus:border-brand/50 focus:outline-none transition-all appearance-none"
                  style={{ fontFamily: FONTS.ui }}
                >
                  <option value="">Select a topic</option>
                  <option value="Deposit Issue">Deposit Issue</option>
                  <option value="Withdrawal Issue">Withdrawal Issue</option>
                  <option value="Account Issue">Account Issue</option>
                  <option value="Game Issue">Game Issue</option>
                  <option value="Bonus/Promotion">Bonus / Promotion</option>
                  <option value="Technical Issue">Technical Issue</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <div>
                <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 mb-1.5 block" style={{ fontFamily: FONTS.head }}>Message</label>
                <textarea
                  name="message" required rows={4} value={formData.message} onChange={handleChange}
                  className="w-full px-4 py-3 rounded-xl text-xs font-bold border border-black/10 dark:border-white/10 bg-white dark:bg-[#111] text-black dark:text-white placeholder-black/30 dark:placeholder-white/30 focus:border-brand/50 focus:outline-none transition-all resize-none"
                  style={{ fontFamily: FONTS.ui }}
                  placeholder="Describe your issue in detail..."
                />
              </div>

              {/* Attachments Section */}
              <div className="space-y-3">
                <label className="text-[9px] font-black uppercase tracking-widest text-black/40 dark:text-white/40 block" style={{ fontFamily: FONTS.head }}>Attachments (Optional)</label>
                <div 
                  onClick={() => fileInputRef.current?.click()}
                  className="w-full p-4 rounded-xl border-2 border-dashed border-black/10 dark:border-white/10 hover:border-brand/40 bg-white/5 cursor-pointer transition-all flex flex-col items-center justify-center gap-2 group"
                >
                  <FaPaperclip className="text-xl text-black/20 dark:text-white/20 group-hover:text-brand/60 transition-colors" />
                  <span className="text-[10px] font-bold text-black/40 dark:text-white/40 uppercase tracking-widest">Click to upload files</span>
                  <input 
                    type="file" ref={fileInputRef} multiple onChange={handleFileChange} className="hidden" 
                    accept="image/*,.pdf,.doc,.docx"
                  />
                </div>

                {attachments.length > 0 && (
                  <div className="flex flex-wrap gap-2 pt-1">
                    {attachments.map((file, i) => (
                      <div key={'file'+i} className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 animate-fadeIn">
                        <span className="text-[10px] font-bold text-black/60 dark:text-white/60 truncate max-w-[150px]">{file.name}</span>
                        <button 
                          type="button" onClick={() => removeAttachment(i)}
                          className="text-red-500/60 hover:text-red-500 transition-colors"
                        >
                          <FaTimes size={10} />
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              <button
                type="submit" disabled={submitting}
                className="w-full py-4 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] text-black transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 disabled:opacity-50 shadow-xl"
                style={{ background: COLORS.brandGradient }}
              >
                {submitting ? (
                  <div className="w-4 h-4 border-2 border-black/30 border-t-black rounded-full animate-spin"></div>
                ) : (
                  <>
                    <FaPaperPlane className="text-xs" />
                    Submit Support Ticket
                  </>
                )}
              </button>
            </form>
          )}
        </div>
      </div>

      {/* Footer */}
      <div className="pb-8 text-center opacity-5 select-none pointer-events-none">
        <p className="text-[9px] font-black uppercase tracking-[2em] ml-[2em]">Support</p>
      </div>
    </div>
  );
};

export default ContactUs;
