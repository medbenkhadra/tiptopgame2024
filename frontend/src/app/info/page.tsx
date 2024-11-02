"use client";
import styles from '../../styles/page.module.css'
import React, {useEffect} from "react";
import { useState } from "react";
import {Col, Row, Tabs} from 'antd';
import Dayjs from 'dayjs';
import 'dayjs/locale/fr';
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import PoliticsTab from "@/app/info/components/PoliticsTab";
import MentionsLegalesTab from "@/app/info/components/MentionsLegalesTab";
import CGU from "@/app/info/components/CGU";
import Head from "next/head";

Dayjs.locale('fr');

export default function Info() {

    const [loading, setLoading] = useState(true);

    const [stepWindow, setStepWindow] = useState(0);

    useEffect(() => {
        setTimeout(() => {
            setLoading(false);
        }, 500);
    }, []);

    const [activeTab, setActiveTab] = useState<string>('1');
    const onTabChange = (key: string) => {
        console.log(key);
        setActiveTab(key);
    };

    return (
      <>
          <Head>
              <title>TipTop - Politique Générale</title>
          </Head>
          {loading && (
              <>
                  <main className={styles.main}>
                  <SpinnigLoader></SpinnigLoader>
                    </main>
              </>
          )}
          {!loading && (
              <>
                  <main className={`${styles.main} mt-5 pt-5 justify-content-start`}>

                                          <Row className={`${styles.fullWidthElement}`}>
                          <Col className={`${styles.fullWidthElement} px-5 mx-5`}>
                              <Tabs activeKey={activeTab} onChange={onTabChange}>
                                  <Tabs.TabPane key='1' tab={`Politique de confidentialité`} >
                                      <PoliticsTab></PoliticsTab>
                                  </Tabs.TabPane>

                                    <Tabs.TabPane key='2' tab={`Mentions légales`} >
                                        <MentionsLegalesTab></MentionsLegalesTab>
                                    </Tabs.TabPane>


                                  <Tabs.TabPane key='3' tab={`Conditions Générales d'Utilisation`} >
                                      <CGU></CGU>
                                  </Tabs.TabPane>


                              </Tabs>
                          </Col>
                      </Row>


                  </main>
              </>
          )}


      </>
  )
}
