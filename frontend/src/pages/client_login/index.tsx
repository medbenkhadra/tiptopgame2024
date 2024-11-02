
import React, { useState, useEffect } from 'react'
import { Container, Row, Col } from 'react-bootstrap';
import { Button } from 'antd';
import '../../app/globals.css'
import '../../app/responsive.css'

import Dayjs from 'dayjs';
import 'dayjs/locale/fr';
import styles from '../../styles/pages/auth/clientLoginPage.module.css';
import LoginClientForm from './components/LoginClientForm';
import RegisterClientForm from './components/RegisterClientForm';
import RedirectService from '../../app/service/RedirectService';
Dayjs.locale('fr');
import Image from 'next/image';
import loginFormImg from "@/assets/images/loginForm.jpg";
import registerFormImg from "@/assets/images/registerForm.jpg";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import Head from "next/head";

export default function userLogin() {

  const { redirectAdminToToDashboard } = RedirectService();
  const [formStep, setFormStep] = useState(1);

    const handleFormStepChange = () => {
        setFormStep(formStep === 1 ? 2 : 1);
        setLoading(true);
        setTimeout(() => {
            setLoading(false);
        }, 350);
    };

  useEffect(() => {
      redirectAdminToToDashboard();
  }, [])


    const [loading, setLoading] = useState(true);
    useEffect(() => {
        setLoading(true);
        setTimeout(() => {
            setLoading(false);
        }, 1000);
    }, [formStep]);

  return (
      <>
          <Head>
              <title>TipTop - Se connecter</title>
          </Head>
      {loading && (
          <>
          <SpinnigLoader></SpinnigLoader>
          </>
      )}

          {!loading && (
              <>
    <div>
      <Row className={`${styles.loginPageMainDiv} m-0`}>
        {formStep == 1 ? (
          <>
            <Col className={`${styles.loginPageMainDivLeftSide} p-0 m-0`} xl={8} sm={0} md={3}>
                <Image
                    src={loginFormImg}
                    alt="Landing Image"
                >

                </Image>
            </Col>
          </>

        ) : (
          <>
            <Col className={`${styles.loginPageMainDivLeftSide} p-0 m-0`} xl={7}  md={4} sm={0}>
                <Image
                src={registerFormImg}
                alt="Landing Image"
                >

                </Image>
            </Col>

          </>
        )}


        {formStep == 1 ? (
          <>
            <Col className={`${styles.loginPageMainDivRightSide}`} xl={4}   md={9}  sm={12} >
              <LoginClientForm  formStep={formStep} handleFormStepChange={handleFormStepChange}></LoginClientForm>
            </Col>
          </>

        ) : (
          <>
            <Col className={`${styles.loginPageMainDivRightSide}`} xl={5}   md={8}  sm={12}>
              <RegisterClientForm formStep={formStep} handleFormStepChange={handleFormStepChange}></RegisterClientForm>
            </Col>

          </>
        )}
      </Row>
    </div>
              </>
          )}


      </>
  )
}

