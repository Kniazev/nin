<?php
/**
 * Created by PhpStorm.
 * User: knyazev
 * Date: 21.10.19
 * Time: 17.12
 */

namespace Nin\NinTheme\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class AddOrdersAndListsBlock implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * AddLinksBlock constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockFactory = $blockFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function apply()
    {

        $identifier = 'order-and-links-block';
        $cmsBlockData = [
            'title' => 'Order and links block',
            'identifier' => $identifier,
            'content' => $this->getContent(),
            'is_active' => 1,
            'stores' => [0],
        ];

        $this->moduleDataSetup->getConnection()->startSetup();

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier)->create();
        $searchResult = $this->blockRepository->getList($searchCriteria);

        if ($searchResult->getTotalCount() > 0) {
            $items = $searchResult->getItems();
            $block = array_shift($items);
        } else {
            $block = $this->blockFactory->create();
        }
        $block->addData($cmsBlockData);
        $this->blockRepository->save($block);

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
        return '0.0.1';
    }

    private function getContent()
    {
        return <<<CONTENT
   <ul id="menu"
      data-mage-init='{"menu":{"responsive":true, "expanded":true, "delay": 200, "position":{"my":"left top","at":"left+10 top+30"}}}'>
      <li class="level0 level-top parent ui-menu-item">
        <a href="#" class="level-top ui-corner-all" id="ui-id-2" tabindex="-1" role="menuitem"><span>Orders & Lists</span></a>
        <ul class="level0 submenu ui-menu ui-widget ui-widget-content ui-corner-all">
          <li class="ui-menu-item"><a href="#" class="ui-state-focus">Order History</a></li>
          <li class="ui-menu-item"><a href="#" class="ui-state-focus">Address Book</a></li>
          <li class="ui-menu-item"><a href="#" class="ui-state-focus">Wish List</a></li>
        </ul>
      </li>
    </ul>
CONTENT;
    }
}
