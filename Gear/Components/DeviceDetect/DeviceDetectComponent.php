<?php

namespace Gear\Components\DeviceDetect;

use Gear\Components\DeviceDetect\Interfaces\DeviceDetectComponentInterface;
use Gear\Library\Services\Component;

/**
 * Компонент для определения на каком устройстве открыт сайт
 * - мобильный
 * - планшет
 * - десктоп
 *
 * @package Gear Framework
 *
 * @property string|null    detectType
 * @property array          httpHeaders
 * @property array|null     mobileDetectionRules
 * @property array|null     mobileDetectionRulesExtended
 * @property string|null    userAgent
 *
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class DeviceDetectComponent extends Component implements DeviceDetectComponentInterface
{
    /* Traits */
    /* Const */
    /* Private */
    private ?string $detectionType = null;
    private array $httpHeaders = [];
    private bool $isDesktop = false;
    private bool $isMobile = false;
    private bool $isTablet = false;
    private ?array $matchesArray = null;
    private ?string $matchingRegex = null;
    private ?array $mobileDetectionRules = null;
    private ?array $mobileDetectionRulesExtended = null;
    private ?string $userAgent = null;
    /* Protected */
    /* Public */

    /**
     * Проверка мобильных заголовков
     *
     * @return bool
     */
    public function checkHttpHeadersForMobile(): bool
    {
        foreach (self::MOBILE_HEADERS as $mobileHeader => $matchType) {
            if (isset($this->httpHeaders[$mobileHeader]) === false) {
                continue;
            }
            if (is_array($matchType['matches']) === false) {
                return true;
            }
            foreach ($matchType['matches'] as $match) {
                if (str_contains($this->httpHeaders[$mobileHeader], $match)) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Тип детектируемого в данный момент устройства
     *
     * @return string|null
     */
    public function getDetectionType(): ?string
    {
        return $this->detectionType;
    }

    /**
     * Возвращает текущие клиентские заголовки
     *
     * @return array
     */
    public function getHttpHeaders(): array
    {
        return $this->httpHeaders;
    }

    /**
     * Возвращает правила для детектирования устройства
     *
     * @return array|null
     */
    public function getRules(): ?array
    {
        if ($this->detectionType === self::DETECTION_TYPE_EXTENDED) {
            return $this->mobileDetectionRulesExtended;
        }
        return $this->mobileDetectionRules;
    }

    /**
     * Возвращает правила для детектирования мобильных устройств
     *
     * @return array
     */
    public function getMobileDetectionRules(): array
    {
        if (empty($this->mobileDetectionRules)) {
            $this->mobileDetectionRules = array_merge(
                self::PHONE_DEVICES,
                self::TABLET_DEVICES,
                self::OPERATING_SYSTEM,
                self::BROWSERS,
            );
        }
        return $this->mobileDetectionRules;
    }

    /**
     * Возвращает расширенные правила для детектирования мобильных устройств
     *
     * @return array
     */
    public function getMobileDetectionRulesExtended(): array
    {
        if ($this->mobileDetectionRulesExtended === null) {
            $this->mobileDetectionRulesExtended = array_merge(
                self::PHONE_DEVICES,
                self::TABLET_DEVICES,
                self::OPERATING_SYSTEM,
                self::BROWSERS,
                self::UTILITIES,
            );
        }
        return $this->mobileDetectionRulesExtended;
    }

    /**
     * Возвращает пользовательский USER-AGENT
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        if ($this->userAgent === null) {
            foreach (self::UA_HTTP_HEADERS as $altHeader) {
                if (empty($this->httpHeaders[$altHeader]) === false) {
                    $this->userAgent .= $this->httpHeaders[$altHeader] . " ";
                }
            }
        }
        return $this->_userAgent;
    }

    /**
     * Возвращает true, если зашли с десктопа
     *
     * @return bool
     */
    public function isDesktop(): bool
    {
        if ($this->isDesktop === null) {
            $this->isDesktop = $this->isMobile() === false && $this->isTablet() === false;
        }
        return $this->isDesktop;
    }

    /**
     * Возвращает true, если зашли с мобильного телефона
     * @return bool
     */
    public function isMobile(): bool
    {
        if ($this->isMobile === null) {
            $this->detectType = self::DETECTION_TYPE_MOBILE;
            $this->isMobile = $this->checkHttpHeadersForMobile() || $this->matchDetectionRulesAgainstUA();
        }
        return $this->isMobile;
    }

    /**
     * Возвращает true, если зашли с планшета
     *
     * @return bool
     */
    public function isTablet(): bool
    {
        if ($this->isTablet === null) {
            $this->isTablet = false;
            $this->detectType = self::DETECTION_TYPE_MOBILE;
            foreach (self::TABLET_DEVICES as $regex) {
                if ($this->match($regex, $this->userAgent)) {
                    $this->isTablet = true;
                    break;
                }
            }
        }
        return $this->isTablet;
    }

    /**
     * Возвращает true если шаблон совпал с USER-AGENT
     *
     * @param   string|null $regex
     * @param   string|null $userAgent
     * @return  bool
     */
    public function match(?string $regex, ?string $userAgent = null): bool
    {
        $regex = str_replace('/', '\/', $regex);
        $match = (bool)preg_match("/{$regex}/is", ($userAgent ?: $this->userAgent), $matches);
        if ($match) {
            $this->matchingRegex = $regex;
            $this->matchesArray = $matches;
        }
        return $match;
    }

    public function matchDetectionRulesAgainstUA(?string $userAgent = null): bool
    {
        $match = false;
        foreach ($this->getRules() as $regex) {
            if ($regex && $this->match($regex, $userAgent)) {
                $match = true;
                break;
            }
        }
        return $match;
    }

    public function onAfterInstallService(): void
    {
        $this->httpHeaders = $_SERVER;
    }

    public function setDetectionType(string $type): void
    {
        $this->detectionType = $type;
    }

    public function setHttpHeaders(array $headers): void
    {
        $this->httpHeaders = [];
        foreach ($headers as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $this->httpHeaders[$key] = $value;
            }
        }
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }
}
