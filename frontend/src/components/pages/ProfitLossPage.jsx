import React from 'react'
import Navbar from '../navbar/Navbar'
import BettingTransactionPage from '../sidebar-components/statements/BettingTransaction'
import { useColors } from '../../hooks/useColors';
function ProfitLossPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <BettingTransactionPage />
      </div>
    </div>
  )
}

export default ProfitLossPage

