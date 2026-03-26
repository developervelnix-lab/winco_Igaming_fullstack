
import React from 'react'
import ExclusionPolicy from '../sidebar-components/legal-complience/Exclusion'
import Navbar from '../navbar/Navbar'

function ExclusionPolicyPage() {
  return (
      <div className="bg-black min-h-screen">
        <Navbar/>
        <div className='pt-20'>
           <ExclusionPolicy/>
        </div>
      </div>
  )
}

export default ExclusionPolicyPage