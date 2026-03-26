import React from 'react'
import RulesAndRegulation from '../sidebar-components/legal-complience/RulesAndRegulation'
import Navbar from '../navbar/Navbar'
import { useColors } from '../../hooks/useColors';
function RulesAndRegulationPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <RulesAndRegulation />
      </div>
    </div>
  )
}

export default RulesAndRegulationPage