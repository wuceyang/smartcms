<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Redis;

/**
 * @group commands
 * @group realm-pubsub
 */
class PUNSUBSCRIBE_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\Redis\PUNSUBSCRIBE';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PUNSUBSCRIBE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('channel:foo:*', 'channel:bar:*');
        $expected = array('channel:foo:*', 'channel:bar:*');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('channel:foo:*', 'channel:bar:*'));
        $expected = array('channel:foo:*', 'channel:bar:*');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('punsubscribe', 'channel:*', 1);
        $expected = array('punsubscribe', 'channel:*', 1);

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testDoesNotSwitchToSubscribeMode()
    {
        $redis = $this->getClient();

        $this->assertSame(array('punsubscribe', 'channel:*', 0), $redis->punsubscribe('channel:*'));
        $this->assertSame('echoed', $redis->echo('echoed'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testUnsubscribesFromNotSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('punsubscribe', 'channel:*', 0), $redis->punsubscribe('channel:*'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testUnsubscribesFromSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));
        $this->assertSame(array('punsubscribe', 'channel:*', 2), $redis->punsubscribe('channel:*'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.6.0
     */
    public function testUnsubscribesFromAllSubscribedChannels()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));
        $this->assertSame(array('punsubscribe', null, 2), $redis->punsubscribe());
    }
}
