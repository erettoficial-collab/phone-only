<?php

namespace erettn\PhoneOnly;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        // Registra los eventos para escuchar las conexiones entrantes
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerPreLogin(PlayerPreLoginEvent $event): void {
        $playerInfo = $event->getPlayerInfo();
        
        // Extrae los datos del paquete de datos de conexión (Metadatos de red)
        $os = $playerInfo->getExtraData()["DeviceOS"] ?? DeviceOS::UNKNOWN;
        $deviceModel = $playerInfo->getExtraData()["DeviceModel"] ?? "";

        // Códigos numéricos de PocketMine para Android (1) e iOS (2)
        $celularesPermitidos = [DeviceOS::ANDROID, DeviceOS::IOS];

        // 1. FILTRO DE PC, CONSOLAS Y EMULADORES (Bloquea todo lo que no sea Android o iOS nativo)
        if (!in_array($os, $celularesPermitidos)) {
            $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, "§c[PhoneOnly] Solo se permite el acceso desde teléfonos celulares.");
            return;
        }

        // 2. FILTRO DE TABLETS (Escanea el modelo interno para descartar iPads o Tablets Android)
        $modelLower = strtolower($deviceModel);
        if (strpos($modelLower, "ipad") !== false || strpos($modelLower, "tablet") !== false || strpos($modelLower, "tab") !== false) {
            $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_PLUGIN, "§c[PhoneOnly] Las tablets no están permitidas en este servidor.");
            return;
        }
    }
}
