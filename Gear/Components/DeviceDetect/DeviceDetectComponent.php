<?php

namespace Gear\Components\DeviceDetect;

use Gear\Components\DeviceDetect\Interfaces\DeviceDetectComponentInterface;
use Gear\Library\GComponent;

/**
 * Компонент для определения на каком устройстве открыт сайт
 * - мобильный
 * - планшет
 * - десктоп
 *
 * @package Gear Framework
 *
 * @property string|null detectType
 * @property array httpHeaders
 * @property array|null mobileDetectionRules
 * @property array|null mobileDetectionRulesExtended
 * @property string|null userAgent
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class DeviceDetectComponent extends GComponent implements DeviceDetectComponentInterface
{
    /* Traits */
    /* Const */
    /* Private */
    private ?string $_detectionType = null;
    private array $_httpHeaders = [];
    private ?bool $_isDesktop = null;
    private ?bool $_isMobile = null;
    private ?bool $_isTablet = null;
    private ?array $_matchesArray = null;
    private ?string $_matchingRegex = null;
    private ?array $_mobileDetectionRules = null;
    private ?array $_mobileDetectionRulesExtended = null;
    private ?string $_userAgent = null;
    /* Protected */
    /* Public */

    /**
     * Проверка мобильных заголовков
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function checkHttpHeadersForMobile()
    {
        foreach (self::MOBILE_HEADERS as $mobileHeader => $matchType) {
            if (isset($this->httpHeaders[$mobileHeader]) ) {
                if (is_array($matchType['matches']) ) {
                    foreach ($matchType['matches'] as $_match) {
                        if (strpos($this->httpHeaders[$mobileHeader], $_match) !== false) {
                            return true;
                        }
                    }
                    return false;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Тип детектируемого в данный момент устройства
     *
     * @return string|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getDetectionType(): ?string
    {
        return $this->_detectionType;
    }

    /**
     * Возвращает текущие клиентские заголовки
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getHttpHeaders(): array
    {
        return $this->_httpHeaders;
    }

    /**
     * Возвращает правила для детектирования устройства
     *
     * @return array|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getRules()
    {
        if ($this->detectionType == self::DETECTION_TYPE_EXTENDED) {
            return $this->mobileDetectionRulesExtended;
        } else {
            return $this->mobileDetectionRules;
        }
    }

    /**
     * Возвращает правила для детектирования мобильных устройств
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getMobileDetectionRules(): array
    {
        if (!$this->_mobileDetectionRules) {
            $this->_mobileDetectionRules = array_merge(
                self::PHONE_DEVICES,
                self::TABLET_DEVICES,
                self::OPERATING_SYSTEM,
                self::BROWSERS,
            );
        }
        return $this->_mobileDetectionRules;
    }

    /**
     * Возвращает расширенные правила для детектирования мобильных устройств
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getMobileDetectionRulesExtended(): array
    {
        if ($this->_mobileDetectionRulesExtended === null) {
            $this->_mobileDetectionRulesExtended = array_merge(
                self::PHONE_DEVICES,
                self::TABLET_DEVICES,
                self::OPERATING_SYSTEM,
                self::BROWSERS,
                self::UTILITIES,
            );
        }
        return $this->_mobileDetectionRulesExtended;
    }

    /**
     * Возвращает пользовательский USER-AGENT
     * @return string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getUserAgent(): string
    {
        if ($this->_userAgent === null) {
            foreach (self::UA_HTTP_HEADERS as $altHeader) {
                if (!empty($this->_httpHeaders[$altHeader])) {
                    $this->_userAgent .= $this->httpHeaders[$altHeader] . " ";
                }
            }
        }
        return $this->_userAgent;
    }

    /**
     * Возвращает true, если зашли с десктопа
     *
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isDesktop(): bool
    {
        if ($this->_isDesktop === null) {
            $this->_isDesktop = $this->isMobile() === false && !$this->isTablet() === false;
        }
        return $this->_isDesktop;
    }

    /**
     * Возвращает true, если зашли с мобильного телефона
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isMobile(): bool
    {
        if ($this->_isMobile === null) {
            $this->detectType = self::DETECTION_TYPE_MOBILE;
            if ($this->checkHttpHeadersForMobile()) {
                $this->_isMobile = true;
            } else {
                $this->_isMobile = $this->matchDetectionRulesAgainstUA();
            }
        }
        return $this->_isMobile;
    }

    /**
     * Возвращает true, если зашли с планшета
     *
     * @return bool|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function isTablet(): bool
    {
        if ($this->_isTablet === null) {
            $this->_isTablet = false;
            $this->detectType = self::DETECTION_TYPE_MOBILE;
            foreach (self::TABLET_DEVICES as $_regex) {
                if ($this->match($_regex, $this->userAgent)) {
                    $this->_isTablet = true;
                    break;
                }
            }
        }
        return $this->_isTablet;
    }

    /**
     * Возвращает true если шаблон совпал с USER-AGENT
     *
     * @param string|null $regex
     * @param string|null $userAgent
     * @return bool
     * @since 0.0.2
     * @version 0.0.2
     */
    public function match(?string $regex, ?string $userAgent = null): bool
    {
        $regex = str_replace('/', '\/', $regex);
        $match = (bool)preg_match('/'.$regex.'/is', (!empty($userAgent) ? $userAgent : $this->userAgent), $matches);
        if ($match) {
            $this->_matchingRegex = $regex;
            $this->_matchesArray = $matches;
        }
        return $match;
    }

    public function matchDetectionRulesAgainstUA(?string $userAgent = null): bool
    {
        $match = false;
        foreach ($this->getRules() as $_regex) {
            if (!empty($_regex) && $this->match($_regex, $userAgent)) {
                $match = true;
                break;
            }
        }
        return $match;
    }

    public function onAfterInstallService()
    {
        $this->httpHeaders = $_SERVER;
    }

    public function setDetectionType(string $type)
    {
        $this->_detectionType = $type;
    }

    public function setHttpHeaders(array $headers)
    {
        $this->_httpHeaders = [];
        foreach ($headers as $key => $value) {
            if (substr($key,0,5) == 'HTTP_') {
                $this->_httpHeaders[$key] = $value;
            }
        }
    }

    public function setUserAgent(string $userAgent)
    {
        $this->_userAgent = $userAgent;
    }
}
