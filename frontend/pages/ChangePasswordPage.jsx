import React from 'react'
import Navbar from '../navbar/Navbar'
import PasswordChangeForm from '../sidebar-components/account-actions/ChangePassword'

function ChangePasswordPage() {
  return (
    <div className="bg-black min-h-screen">
       <Navbar/>
       <div className='pt-10'>
          <PasswordChangeForm/>
       </div>
    </div>
  )
}

export default ChangePasswordPage