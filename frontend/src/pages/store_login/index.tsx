import React,{ useEffect} from 'react'
import {Col, Row} from "react-bootstrap";
import styles from "@/styles/pages/auth/adminsLoginPage.module.css";
import LoginAdminstForm from "@/pages/store_login/components/StoreLoginForm";
import Head from "next/head";

export default function index() {



  return (
     <>
         <Head>
             <title>TipTop - Se connecter</title>
         </Head>
         <div>
             <Row className={`${styles.loginPageMainDiv} m-0`}>
                 <>
                     <Col className={`${styles.loginPageMainDivRightSide} pt-3`} md={12} >
                         <LoginAdminstForm></LoginAdminstForm>
                     </Col>
                 </>
             </Row>
         </div>
     </>
  )
}
