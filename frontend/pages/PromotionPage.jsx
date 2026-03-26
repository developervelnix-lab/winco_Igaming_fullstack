import React from 'react'
import Navbar from '../navbar/Navbar'
import Promotion from '../sidebar-components/Miscellaneous/Promotion'

function PromotionPage() {
  return (
    <div className="bg-black min-h-screen">
      <Navbar/>
      <div className='pt-20'>
         <Promotion/>
      </div>
    </div>
  )
}

export default PromotionPage