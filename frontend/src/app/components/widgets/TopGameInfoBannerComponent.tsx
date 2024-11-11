"use client";

import React, {useEffect, useState} from 'react';
import {Col, Tag} from 'antd';
import {CloseOutlined} from "@ant-design/icons";
import {getGameConfig} from "@/app/api";


interface DataType {
    startDate: string;
    time: string;
}

interface TimeRemaining {
    days: number,
    hours: number,
    minutes: number,
    seconds: number
}
export default function TopGameInfoBannerComponent() {
    const [topBannerOpen, setTopBannerOpen] = useState(true);
    const [loading, setLoading] = useState(false);
    const [gameConfig, setGameConfig] = useState<DataType>({got
        startDate: "",
        time: ""
    });


    const [principalPeriodFinishAt, setPrincipalPeriodFinishAt] = useState<DataType>({
        startDate: "",
        time: ""
    });


    const [validationPeriodFinishAt, setValidationPeriodFinishAt] = useState<DataType>({
        startDate: "",
        time: ""
    });




    const [timeRemaining, setTimeRemaining] = useState<TimeRemaining>({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0
    });


    const [gameConfigOriginal, setGameConfigOriginal] = useState<DataType>({
        startDate: "",
        time: ""
    });

    function fetchGameConfig() {

        setLoading(true);


        getGameConfig().then((response) => {
            if (response) {
                setGameConfigOriginal({
                    startDate: response.gameConfig,
                    time: response.time
                })

                setPrincipalPeriodFinishAt({
                    startDate: response.principalPeriodFinishAt.date,
                    time: response.principalPeriodFinishAt.time
                });

                setValidationPeriodFinishAt({
                    startDate: response.validationPeriodFinishAt.date,
                    time: response.validationPeriodFinishAt.time
                });

                setGameStatus(response.gameStatus);

                const originalDate = response.gameConfig;
                const [day, month, year] = originalDate.split('/');


                const formatedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;



                setGameConfig({
                    startDate: formatedDate,
                    time: response.time
                });

                const timeRemainingToStart = response.timeRemainingToStart;
                setTimeRemaining({
                    days: timeRemainingToStart.days,
                    hours: timeRemainingToStart.hours,
                    minutes: timeRemainingToStart.minutes,
                    seconds: timeRemainingToStart.seconds
                });

            }
        }).catch((err) => {
            console.log(err);
        })
        setLoading(false);
    }






    const statusColors: Record<string, string> = {
        "A venir": "processing",
        "En cours": "success",
        "Terminé": "error",
    };


    useEffect(() => {
        setInterval(() => {
            fetchGameConfig();
        }, 10000);

    }, []);


    const [gameStatus, setGameStatus] = useState<string>("");
    const [classColorTag , setClassColorTag] = useState<string>("");
    useEffect(() => {
        setClassColorTag(statusColors[gameStatus] as any);
    }, [gameStatus]);


    return (
      <>
          {topBannerOpen && (
              <>
                  <div className="gameinfo-consent-banner">
                      <div className="gameinfo-consent-banner__inner">
                          <Col span={24}
                               className={"m-0 p-0 d-flex flex-row justify-content-between align-items-center"}>
                              <h5>

                                  {gameStatus === "A venir" && (
                                      <>
                                          Temps restant avant le début du jeu 🚀
                                      </>
                                  )}

                                    {gameStatus === "En cours" && (
                                        <>
                                            Temps restant avant la fin du jeu 🏁
                                        </>
                                    )}

                                  {gameStatus === "Validation" && (
                                        <>
                                            Temps restant avant la fin de la période de validation 🕣
                                        </>
                                  )}

                                    {gameStatus === "Terminé" && (
                                        <>
                                            Le jeu est terminé depuis ✅
                                        </>
                                    )}



                                  <Tag color={classColorTag} className="ms-3">{gameStatus}</Tag>
                                  <br/>
                                  {gameStatus === "En cours" && (
                                      <>
                                          <small>
                                              Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                          </small>
                                      </>
                                  )}

                                  {gameStatus === "A venir" && (
                                      <>
                                          <small>
                                              Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                          </small>
                                      </>
                                  )}

                                  {gameStatus === "Validation" && (
                                      <>
                                          <small>
                                              Du {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time} jusqu'au {validationPeriodFinishAt.startDate} à {validationPeriodFinishAt.time}
                                          </small>
                                      </>
                                  )}

                                  {gameStatus === "Terminé" && (
                                      <>
                                          <small>
                                              Du {gameConfigOriginal.startDate} à {gameConfigOriginal.time} jusqu'au {principalPeriodFinishAt.startDate} à {principalPeriodFinishAt.time}
                                          </small>
                                      </>
                                  )}




                              </h5>
                              <section className="timeContainer">
                                  <div className="wrapper">
                                      <div className={`days ${gameStatus == "Terminé" && "red-bg"}`}>
                                          <h2 id="days">
                                              {timeRemaining.days}
                                          </h2>
                                          Jours
                                      </div>
                                      <div className={`hours ${gameStatus == "Terminé" && "red-bg"}`}>
                                          <h2 id="hours">
                                              {timeRemaining.hours}
                                          </h2>
                                          Heures
                                      </div>
                                      <div className="minutes">
                                          <h2 id="minutes">
                                              {timeRemaining.minutes}
                                          </h2>
                                          Minutes
                                      </div>
                                      <div className="seconds">
                                          <h2 id="seconds">
                                              {timeRemaining.seconds}
                                          </h2>
                                          Secondes
                                      </div>
                                  </div>
                              </section>
                          </Col>





                      </div>
                  </div>
              </>
          )}
      </>
    );
};
