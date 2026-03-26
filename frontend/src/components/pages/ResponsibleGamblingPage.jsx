import React from 'react'
import ResponsibleGambling from '../sidebar-components/legal-complience/ResponsibleGambling'
import Navbar from '../navbar/Navbar'
import { useColors } from '../../hooks/useColors';
function ResponsibleGamblingPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <ResponsibleGambling />
      </div>
    </div>
  )
}

export default ResponsibleGamblingPage


//Aceplayswin@gmail.com