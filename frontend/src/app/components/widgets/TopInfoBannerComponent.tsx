"use client";

import React, {useEffect, useState} from 'react';
import {CheckOutlined, InfoCircleOutlined} from "@ant-design/icons";

export default function TopInfoBannerComponent() {
    const [topBannerOpen, setTopBannerOpen] = useState(false);


    function closeTopBanner() {
        localStorage.setItem('topBannerOpen', 'false');
        setTopBannerOpen(false);
    }

    useEffect(() => {
        if (localStorage.getItem('topBannerOpen') === 'false') {
            setTopBannerOpen(false);
        }else {
            setTopBannerOpen(true);
        }
    }, []);

    return (
      <>
          {topBannerOpen &&(
              <>
                  <div className="cookie-consent-banner">
                      <div className="cookie-consent-banner__inner">
                          <div className="cookie-consent-banner__copy">
                              <div className="cookie-consent-banner__description">
                                  <InfoCircleOutlined className={"me-2"}/> Ce site est un projet étudiant réalisé dans
                                  le cadre
                                  de notre
                                  programme de fin d'année. Nous tenons à souligner que ce site est entièrement dédié à
                                  des fins
                                  éducatives et
                                  de démonstration, et aucune action réelle n'est possible ici.
                              </div>
                          </div>

                          <div className="cookie-consent-banner__actions">
                              <button className="cookie-consent-banner__button" onClick={() => closeTopBanner()}>
                                  J'ai compris <CheckOutlined className={"mx-2"}/>
                              </button>
                          </div>

                      </div>
                  </div>
              </>
          )}
      </>
  );
};
