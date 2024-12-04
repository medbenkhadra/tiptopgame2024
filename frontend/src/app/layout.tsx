"use strict";
import './globals.css'  
import { Inter } from 'next/font/google'
import './responsive.css'

const inter = Inter({ subsets: ['latin'] })

export const metadata = {
  title: 'Jeu concours - Thé Tiptop',
  description: 'Jeu de concours - Jeu de roulette pour gagner des cadeaux - Thé Tiptop - Marque de thé bio et équitable - Cadeaux à gagner',
  keywords: 'jeu concours, jeu de roulette, cadeaux à gagner, thé bio, thé, thé Tiptop, roulette, cadeaux, gagner, jeu, concours, bio, équitable, commerce équitable, commerce, équitable, commerce'
}


import Navbar from './components/widgets/NavbarComponent';
import Footer from './components/widgets/FooterComponent';
import CookiesModalComponent from './components/widgets/CookiesModalComponent';

import { GoogleAnalytics } from '@next/third-parties/google'
import TopInfoBannerComponent from "@/app/components/widgets/TopInfoBannerComponent";


export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {




  return (
      <html lang="en">
      <body className={inter.className}>
      <TopInfoBannerComponent data-testid="top-info-banner"/>

      <Navbar data-testid="navbar"/>
      {children}
      <Footer data-testid="footer"/>
      <CookiesModalComponent data-testid="cookies-modal"/>

      </body>
      <GoogleAnalytics gaId="G-ZKQ99WNM6S"/>
      </html>
  )
}
