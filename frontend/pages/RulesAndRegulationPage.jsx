import React from 'react'
import RulesAndRegulation from '../sidebar-components/legal-complience/RulesAndRegulation'
import Navbar from '../navbar/Navbar'

function RulesAndRegulationPage() {
    return (

        <div className="bg-black min-h-screen">
            <Navbar />
            <div className='pt-16'>
                <RulesAndRegulation />
            </div>
        </div>
    )
}

export default RulesAndRegulationPage