import React from 'react'

function Searchbar() {
    return (
        <>
            {/* Search bar */}
            <div className="order-3 md:order-2 w-full md:w-auto mt-3 md:mt-0 md:flex-1 md:mx-6">
                <div className="relative">
                    <input
                        className="w-full bg-gray-100 dark:bg-white/10 backdrop-blur-sm text-black dark:text-white placeholder:text-gray-300 text-sm border border-red-600/30 rounded-full pl-4 pr-28 py-2.5 transition duration-300 ease focus:outline-none focus:border-amber-400 hover:border-red-500 shadow-md"
                        placeholder="Search Event (At least 3 letters)"
                    />
                    <button
                        className="absolute top-1 right-1 flex items-center rounded-full bg-amber-500 py-1.5 px-4 border border-transparent text-center text-sm text-black dark:text-white font-medium transition-all shadow-sm hover:bg-amber-600 focus:ring-2 focus:ring-amber-300"
                        type="button"
                    >
                        <FontAwesomeIcon icon={faSearch} className="mr-2" />
                        Search
                    </button>
                </div>
            </div>

        </>
    )
}

export default Searchbar