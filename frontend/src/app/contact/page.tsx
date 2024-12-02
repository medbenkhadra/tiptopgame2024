"use client";
import styles from '../../styles/page.module.css'
import React, {useEffect, useRef} from "react";
import { useState } from "react";
import {DatePicker, Modal} from 'antd';
import Dayjs from 'dayjs';
import 'dayjs/locale/fr';
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import {PhoneOutlined, MailOutlined, NodeIndexOutlined, SendOutlined} from "@ant-design/icons";
import ReCAPTCHA from 'react-google-recaptcha'
import Head from "next/head";


Dayjs.locale('fr');

export default function Contact() {

    const [captchaValue, setCaptchaValue] = useState<string | null>(null);
    const recaptcha: any = useRef(null);

    const [loading, setLoading] = useState(true);

    const [stepWindow, setStepWindow] = useState(0);

    useEffect(() => {
        setTimeout(() => {
            setLoading(false);
        }, 500);
    }, []);


    function onChangeRecaptcha(param:any) {
        setCaptchaValue(param);
    }

    function handleSendMessage() {
        if (!captchaValue) {
            Modal.error({
                title: 'Erreur',
                content: 'Veuillez valider le captcha pour continuer'
            });
            return;
        }
        Modal.success({
            title: 'Succès',
            content: 'Votre message a été envoyé avec succès'
        });
    }

    return (
      <>
          <Head>
              <title>thé TipTop - Contact</title>
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
                  <main data-testid="contact-section" className={`${styles.main} mt-5 pt-5`}>
                      <section className="contact" id="contact">
                          <div className="container">
                              <div className="heading text-center">
                                  <h2>Contactez
                                      <span> -Nous </span></h2>
                                  <p>
                                      Pour plus d'informations, contactez-nous via le formulaire ci-dessous
                                      <br/>
                                  </p>
                              </div>
                              <div className="row">
                                  <div className="col-md-5">
                                      <div className="title">
                                          <h3>
                                              Nos Coordonnées
                                          </h3>
                                          <p>
                                              Vous pouvez nous contacter à tout moment pour plus d'informations
                                          </p>
                                      </div>
                                      <div className="content">
                                          <div className="info">
                                              <PhoneOutlined />
                                              <h4 className=" mx-2 d-inline-block">
                                                  TÉLÉPHONE :<br/>
                                                  <br/>
                                                  <span>
                                                      +33 1 23 45 67 89
                                                  </span></h4>
                                          </div>
                                          <div className="info">
                                              <MailOutlined />
                                              <h4 className="mx-2  d-inline-block">EMAIL :
                                                  <br/>
                                                  <span>contact@tiptop.com</span></h4>
                                          </div>
                                          <div className="info">
                                              <NodeIndexOutlined />
                                              <h4 className=" mx-2 d-inline-block">ADRESSE:<br/>
                                                  <span>
                                                      - Siège social : 18 rue Léon Frot, 75011 Paris
                                                  </span></h4>
                                          </div>
                                      </div>
                                  </div>

                                  <div className="col-md-7">

                                      <form>
                                          <div className="row">
                                              <div className="col-sm-6">
                                                  <input type="text" className="form-control" placeholder="Nom et prénom"/>
                                              </div>
                                              <div className="col-sm-6">
                                                  <input type="email" className="form-control" placeholder="E-mail"/>
                                              </div>
                                              <div className="col-sm-12">
                                                  <input type="text" className="form-control" placeholder="Sujet"/>
                                              </div>
                                          </div>
                                          <div className="form-group">
                                              <textarea  className="form-control"  id="comment"
                                                        placeholder="Votre message..."
                                              ></textarea>
                                          </div>

                                          <ReCAPTCHA
                                              //onVerify={onChangeRecaptcha}
                                              ref={recaptcha}
                                              onChange={(e) => {
                                                  if (!e) {
                                                      onChangeRecaptcha(null);
                                                  }else{
                                                        onChangeRecaptcha(e);
                                                  }
                                              }}
                                              onExpired={() => {
                                                  onChangeRecaptcha(null);
                                              }}
                                              sitekey="6LeHhJAqAAAAACJOiw_Q_1EWewLQwwYsHfASFJVH"
                                          />

                                          <button className="btn btn-block" type="button" onClick={() => {
                                             handleSendMessage();
                                          }}>
                                              Envoyer votre message <SendOutlined className={"mx-2"} />
                                          </button>
                                      </form>
                                  </div>
                              </div>
                          </div>
                      </section>
                  </main>
              </>
          )}


      </>
  )
}
