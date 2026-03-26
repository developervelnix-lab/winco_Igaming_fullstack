import React from 'react'
import ResponsibleGambling from '../sidebar-components/legal-complience/ResponsibleGambling'
import Navbar from '../navbar/Navbar'

function ResponsibleGamblingPage() {
  return (
    <div className="bg-black min-h-screen">
       <Navbar/>
       <div className='pt-20'>
           <ResponsibleGambling/>
       </div>
    </div>
  )
}

export default ResponsibleGamblingPage