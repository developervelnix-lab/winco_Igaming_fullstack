import React from 'react'
import Navbar from '../navbar/Navbar'
import InviteAndEarn from '../sidebar-components/Miscellaneous/InviteAndEarn'

function InviteAndEarnPage() {
  return (
    <div className="bg-black min-h-screen">
       <Navbar/>
       <div className='pt-20 px-4 md:px-0'>
          <InviteAndEarn/>
       </div>
    </div>
  )
}

export default InviteAndEarnPage