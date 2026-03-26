import React from 'react'
import Navbar from '../navbar/Navbar'
import GiftCardRedemption from '../sidebar-components/Miscellaneous/GiftCard'

function GifrCardPage() {
  return (
    <div className="bg-black min-h-screen">
       <Navbar />
       <div className='pt-20'>
           <GiftCardRedemption/>
       </div>
    </div>
  )
}

export default GifrCardPage