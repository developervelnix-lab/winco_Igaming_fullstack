import React from 'react'
import Navbar from '../navbar/Navbar'
import InviteAndEarn from '../sidebar-components/Miscellaneous/InviteAndEarn'
import { useColors } from '../../hooks/useColors';
function InviteAndEarnPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <InviteAndEarn />
      </div>
    </div>
  )
}

export default InviteAndEarnPage