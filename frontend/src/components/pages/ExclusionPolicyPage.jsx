import React from 'react'
import ExclusionPolicy from '../sidebar-components/legal-complience/Exclusion'
import Navbar from '../navbar/Navbar'
import { useColors } from '../../hooks/useColors';
function ExclusionPolicyPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <ExclusionPolicy />
      </div>
    </div>
  )
}

export default ExclusionPolicyPage   