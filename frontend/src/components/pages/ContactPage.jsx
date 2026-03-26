import React from 'react'
import ContactUs from '../sidebar-components/contact/ContactUs'
import Navbar from '../navbar/Navbar'
import { useColors } from '../../hooks/useColors';

function ContactPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <ContactUs />
      </div>
    </div>
  )
}

export default ContactPage
