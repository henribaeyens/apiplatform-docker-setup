<?php

declare(strict_types=1);

namespace App\Filter;

use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;

final class JsonListFilter extends Filter
{
    public function filter(ProxyQueryInterface $query, string $alias, string $field, FilterData $data): void
    {
        if (!$data->hasValue()) {
            return;
        }

        $value = $data->getValue();

        if (!\is_array($value)) {
            $data = $data->changeValue([$value]);
        }

        $operator = $data->isType(ContainsOperatorType::TYPE_NOT_CONTAINS) ? 'NOT ' : '';

        $parameterName = $this->getNewParameterName($query);
        $and = $query->getQueryBuilder()->expr()->andX();
        $and->add(sprintf('%sJSON_CONTAINS(%s.%s, :%s) = 1', $operator, $alias, $field, $parameterName));
        $query->getQueryBuilder()->setParameter($parameterName, '"'.$value.'"');

        $this->applyWhere($query, $and);
    }

    public function getDefaultOptions(): array
    {
        return [];
    }

    public function getRenderSettings(): array
    {
        return [ChoiceType::class, [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label' => $this->getLabel(),
        ]];
    }
}
