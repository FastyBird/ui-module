<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:UIModule!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           09.03.20
 */

namespace FastyBird\UIModule;

/**
 * Service constants
 *
 * @package        FastyBird:UIModule!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Constants
{

	/**
	 * Message bus routing keys
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.device';
	public const RABBIT_MQ_DEVICES_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.device';
	public const RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.device';

	// Devices properties
	public const RABBIT_MQ_DEVICES_PROPERTY_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.device.property';
	public const RABBIT_MQ_DEVICES_PROPERTY_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.device.property';
	public const RABBIT_MQ_DEVICES_PROPERTY_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.device.property';

	// Devices configuration
	public const RABBIT_MQ_DEVICES_CONFIGURATION_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.device.configuration';
	public const RABBIT_MQ_DEVICES_CONFIGURATION_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.device.configuration';
	public const RABBIT_MQ_DEVICES_CONFIGURATION_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.device.configuration';

	// Channels
	public const RABBIT_MQ_CHANNELS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.channel';
	public const RABBIT_MQ_CHANNELS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.channel';
	public const RABBIT_MQ_CHANNELS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.channel';

	// Channels properties
	public const RABBIT_MQ_CHANNELS_PROPERTY_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.channel.property';
	public const RABBIT_MQ_CHANNELS_PROPERTY_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.channel.property';
	public const RABBIT_MQ_CHANNELS_PROPERTY_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.channel.property';

	// Channels configuration
	public const RABBIT_MQ_CHANNELS_CONFIGURATION_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.channel.configuration';
	public const RABBIT_MQ_CHANNELS_CONFIGURATION_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.channel.configuration';
	public const RABBIT_MQ_CHANNELS_CONFIGURATION_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.channel.configuration';

	// Data routing keys
	public const RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.device.property';
	public const RABBIT_MQ_DEVICES_CONFIGURATION_DATA_ROUTING_KEY = 'fb.bus.node.data.device.configuration';
	public const RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.property';
	public const RABBIT_MQ_CHANNELS_CONFIGURATION_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.configuration';

	// Accounts
	public const RABBIT_MQ_ACCOUNTS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.account';
	public const RABBIT_MQ_ACCOUNTS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.account';
	public const RABBIT_MQ_ACCOUNTS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.account';

	// Emails
	public const RABBIT_MQ_EMAILS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.email';
	public const RABBIT_MQ_EMAILS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.email';
	public const RABBIT_MQ_EMAILS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.email';

	// Identities
	public const RABBIT_MQ_IDENTITIES_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.identity';
	public const RABBIT_MQ_IDENTITIES_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.identity';
	public const RABBIT_MQ_IDENTITIES_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.identity';

	// Triggers
	public const RABBIT_MQ_TRIGGERS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.trigger';
	public const RABBIT_MQ_TRIGGERS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.trigger';
	public const RABBIT_MQ_TRIGGERS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.trigger';

	// Triggers actions
	public const RABBIT_MQ_TRIGGERS_ACTIONS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.trigger.action';
	public const RABBIT_MQ_TRIGGERS_ACTIONS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.trigger.action';
	public const RABBIT_MQ_TRIGGERS_ACTIONS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.trigger.action';

	// Triggers notifications
	public const RABBIT_MQ_TRIGGERS_NOTIFICATIONS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.trigger.notification';
	public const RABBIT_MQ_TRIGGERS_NOTIFICATIONS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.trigger.notification';
	public const RABBIT_MQ_TRIGGERS_NOTIFICATIONS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.trigger.notification';

	// Triggers conditions
	public const RABBIT_MQ_TRIGGERS_CONDITIONS_CREATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.created.trigger.condition';
	public const RABBIT_MQ_TRIGGERS_CONDITIONS_UPDATED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.updated.trigger.condition';
	public const RABBIT_MQ_TRIGGERS_CONDITIONS_DELETED_ENTITY_ROUTING_KEY = 'fb.bus.node.entity.deleted.trigger.condition';

	/**
	 * Microservices origins
	 */

	public const NODE_DEVICES_ORIGIN = 'com.fastybird.devices-module';
	public const NODE_AUTH_ORIGIN = 'com.fastybird.auth-module';
	public const NODE_TRIGGERS_ORIGIN = 'com.fastybird.triggers-module';

	public const WS_HEADER_AUTHORIZATION = 'authorization';
	public const WS_HEADER_WS_KEY = 'x-ws-key';
	public const WS_HEADER_ORIGIN = 'origin';

}
