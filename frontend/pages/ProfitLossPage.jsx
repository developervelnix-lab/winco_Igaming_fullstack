import React from 'react'
import Navbar from '../navbar/Navbar'
import BettingTransactionPage from '../sidebar-components/statements/BettingTransaction'

function ProfitLossPage() {
  return (
    <>
      <Navbar/>
      <div className='pt-20 md:pt-24'>
        <BettingTransactionPage/>
      </div>
    </>
  )
}

export default ProfitLossPage