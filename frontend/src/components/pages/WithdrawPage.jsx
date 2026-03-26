import React from 'react'
import Navbar from '../navbar/Navbar'
import Withdraw from '../navbar/Withdraw'

function WithdrawPage() {
  return (
    <>
      <Navbar/>

      <div className='mt-[120px]'>
        <Withdraw/>
      </div>

    </>
  )
}

export default WithdrawPage