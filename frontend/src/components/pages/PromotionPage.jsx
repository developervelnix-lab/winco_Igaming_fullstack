import React from 'react'
import Navbar from '../navbar/Navbar'
import Promotion from '../sidebar-components/Miscellaneous/Promotion'
import { useColors } from '../../hooks/useColors';
function PromotionPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <Promotion />
      </div>
    </div>
  )
}

export default PromotionPage