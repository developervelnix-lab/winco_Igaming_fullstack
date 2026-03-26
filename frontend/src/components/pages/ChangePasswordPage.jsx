import React from 'react'
import Navbar from '../navbar/Navbar'
import PasswordChangeForm from '../sidebar-components/account-actions/ChangePassword'
import { useColors } from '../../hooks/useColors';
function ChangePasswordPage() {
  const COLORS = useColors();
  return (
    <div className="min-h-screen" style={{ backgroundColor: COLORS.bg }}>
      <Navbar />
      <div className='pt-[140px] md:pt-[160px] pb-10 px-2'>
        <PasswordChangeForm />
      </div>
    </div>
  )
}

export default ChangePasswordPage