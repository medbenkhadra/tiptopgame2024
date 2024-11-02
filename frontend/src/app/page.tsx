"use client";
import styles from '../styles/page.module.css'
import React, {useEffect} from "react";
import { useState } from "react";
import { DatePicker } from 'antd';
import LandingPageTopSection from './components/landingpage/LandingPageTopSection';
import Dayjs from 'dayjs';
import 'dayjs/locale/fr';
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import Head from 'next/head';
Dayjs.locale('fr');

export default function Home() {

    const [loading, setLoading] = useState(true);

    const [stepWindow, setStepWindow] = useState(0);

    useEffect(() => {
        setTimeout(() => {
            setLoading(false);
        }, 500);
    }, []);
  
  return (
      <>

          {loading && (
              <>
                  <main className={styles.main}>
                      <SpinnigLoader></SpinnigLoader>
                  </main>
              </>
          )}
          {!loading && (
              <>
                  <main className={styles.main}>
                      <LandingPageTopSection></LandingPageTopSection>
                  </main>
              </>
          )}


      </>
  )
}
