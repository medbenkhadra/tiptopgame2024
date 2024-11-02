"use client";
import styles from '../../styles/page.module.css';
import React, { useEffect, useState } from "react";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import 'dayjs/locale/fr';
import Dayjs from 'dayjs';
import {BulbOutlined, ExclamationCircleOutlined, StarOutlined} from "@ant-design/icons";

Dayjs.locale('fr');
import wallpaperHomepageImg from '@/assets/images/wallpaperHomepage.png';
import Image from 'next/image';

export default function About() {

    const [loading, setLoading] = useState(true);

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
                    <main data-testid="about-section" className={`${styles.main} mt-5 pt-5`}>
                        <section className="section_all" id="about">
                            <div className="container contact">
                                <div className="heading text-center">
                                    <h2>A propos de
                                        <span> Nous </span></h2>
                                    <p>
                                        Le Grand Jeu-Concours Thé Tip Top vous invite à une aventure exceptionnelle
                                        où vous pourrez découvrir nos thés bios et tenter de remporter des cadeaux
                                        exclusifs. Scannez simplement le code unique de votre ticket de caisse et
                                        lancez-vous dans la course aux récompenses inoubliables !
                                    </p>
                                </div>

                                <div data-testid="about-content" className="row">
                                    <div className="col-lg-12">
                                        <div className="section_title_all text-center">
                                            <p className="section_subtitle mx-auto text-muted">
                                                Chez Thé Tip Top, nous sommes passionnés par l'art du thé. Depuis nos débuts,
                                                nous nous engageons à offrir des thés bios de qualité supérieure, créés
                                                méticuleusement à la main pour une expérience sensorielle inégalée.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="row vertical_content_manage mt-5">
                                    <div className="col-lg-6">
                                        <div className="about_header_main mt-3">
                                            <div className="about_icon_box">
                                                <p className="text_custom font-weight-bold">
                                                    Bienvenue chez Thé Tip Top
                                                </p>
                                            </div>
                                            <h4 className="about_heading text-capitalize font-weight-bold mt-4">
                                                Une expérience de thé exceptionnelle
                                            </h4>
                                            <p className="text-muted mt-3">
                                                Thé Tip Top, une entreprise familiale passionnée par la création de thés bios
                                                et faits à la main. Notre mission est de vous offrir une expérience de thé
                                                unique et inoubliable.
                                            </p>

                                            <p className="text-muted mt-3">
                                                Rejoignez-nous dès maintenant et tentez votre chance de gagner des récompenses
                                                inoubliables tout en découvrant nos délicieux thés.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="col-lg-6">
                                        <div className="img_about mt-3">
                                            <Image data-testid="about-image" src={wallpaperHomepageImg} alt={"wallpaper"} style={{borderRadius: "10px", width: "40rem", height: "20rem"}}>

                                            </Image>
                                        </div>
                                    </div>
                                </div>

                                <div className="row mt-3">
                                    <div className="col-lg-4">
                                        <div className="about_content_box_all mt-3">
                                            <div className="about_detail text-center">
                                                <div className="about_icon">
                                                    <BulbOutlined className={"fs-1"}/>

                                                </div>
                                                <h5 className="text-dark text-capitalize mt-3 font-weight-bold">
                                                    Créativité et Qualité
                                                </h5>
                                                <p className="edu_desc mt-3 mb-0 text-muted">
                                                    Chez Thé Tip Top, nous mettons l'accent sur la créativité dans la conception
                                                    de nos thés et nous garantissons une qualité exceptionnelle à chaque
                                                    dégustation.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-4">
                                        <div className="about_content_box_all mt-3">
                                            <div className="about_detail text-center">
                                                <div className="about_icon">
                                                    <ExclamationCircleOutlined className={"fs-1"} />
                                                </div>
                                                <h5 className="text-dark text-capitalize mt-3 font-weight-bold">
                                                    Équilibre des Saveurs
                                                </h5>
                                                <p className="edu_desc mb-0 mt-3 text-muted">
                                                    Notre engagement envers l'équilibre des saveurs garantit une expérience
                                                    gustative harmonieuse pour tous les amateurs de thé.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-4">
                                        <div className="about_content_box_all mt-3">
                                            <div className="about_detail text-center">
                                                <div className="about_icon">
                                                    <StarOutlined className={"fs-1"} />
                                                </div>
                                                <h5 className="text-dark text-capitalize mt-3 font-weight-bold">
                                                    Thés Bios et Faits à la Main
                                                </h5>
                                                <p className="edu_desc mb-0 mt-3 text-muted">
                                                    Nous sommes fiers de proposer des thés bios, créés avec soin à la main,
                                                    respectant la nature et votre bien-être.
                                                </p>
                                            </div>
                                        </div>
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
