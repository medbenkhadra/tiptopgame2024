import React,{ useEffect} from 'react'
import {Col, Row} from "react-bootstrap";
import styles from "@/styles/pages/auth/adminsLoginPage.module.css";
import ResetPasswordForm from "./components/ResetPasswordForm";
import Head from "next/head";
export default function index() {



  return (
     <>
         <Head>
             <title>TipTop - Réinitialisation du mot de passe</title>
         </Head>
         <div>
             <Row className={`${styles.loginPageMainDiv} m-0`}>
                 <>
                     <Col className={`${styles.loginPageMainDivRightSide} pt-3`} md={12} >
                         <ResetPasswordForm></ResetPasswordForm>
                     </Col>
                 </>
             </Row>
         </div>
     </>
  )
}
